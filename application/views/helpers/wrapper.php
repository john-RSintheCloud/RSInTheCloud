<?php

/**
 * view helpers Wrapper
 * A wrapper round the view helpers which display html directly.
 *
 * Functions included in here are only required by layout and view scripts,
 * not by business logic.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

# Blank the header insert - used to add js and styles into the header
$headerinsert = "";

function nicedate($date, $time = false, $wordy = true) {
    # format a MySQL ISO date
    # Always use the 'wordy' style  as this works better internationally.
    global $lang;
    $y = substr($date, 0, 4);
    if (($y == "") || ($y == "0000"))
        return "-";
    $m = @$lang["months"][substr($date, 5, 2) - 1];
    if ($m == "")
        return $y;
    $d = substr($date, 8, 2);
    if ($d == "" || $d == "00")
        return $m . " " . $y;
    $t = $time ? (" @ " . substr($date, 11, 5)) : "";
    return $d . " " . $m . " " . substr($y, 2, 2) . $t;
}


function pager($break = true)
{
    global $curpage, $url, $totalpages, $offset, $per_page, $lang, $jumpcount, $pager_dropdown;
    $jumpcount++;
    global $pagename;
    if ($totalpages != 0 && $totalpages != 1) {
?>
                <span class="HorizontalWhiteNav"><?php if ($break) { ?>&nbsp;<br /><?php } hook("custompagerstyle");
        if ($curpage > 1) {
?><a class="prevLink" href="<?php echo $url ?>&go=prev&offset=<?php echo urlencode($offset - $per_page) ?>" onClick="return CentralSpaceLoad(this, true);"><?php } ?>&lt;&nbsp;<?php echo $lang["previous"] ?><?php if ($curpage > 1) { ?></a><?php } ?>&nbsp;|

        <?php
        if ($pager_dropdown) {
            $id = rand();
        ?>
                            <select id="pager<?php echo $id; ?>" class="ListDropdown" style="width:50px;" onChange="var jumpto = document.getElementById('pager<?php echo $id ?>').value;
                                    if ((jumpto > 0) && (jumpto <=<?php echo $totalpages ?>)) {
                                        return CentralSpaceLoad('<?php echo $url ?>&go=page&offset=' + ((jumpto - 1) * <?php echo urlencode($per_page) ?>), true);
                                    }">
            <?php for ($n = 1; $n < $totalpages + 1; $n++) { ?>
                                        <option value='<?php echo $n ?>' <?php if ($n == $curpage) { ?>selected<?php } ?>><?php echo $n ?></option>
            <?php } ?>
                            </select>
        <?php } else { ?>
                            <a href="#" title="<?php echo $lang["jumptopage"] ?>" onClick="p = document.getElementById('jumppanel<?php echo $jumpcount ?>');
                                    if (p.style.display != 'block') {
                                        p.style.display = 'block';
                                        document.getElementById('jumpto<?php echo $jumpcount ?>').focus();
                                    } else {
                                        p.style.display = 'none';
                                    }
                                    ;
                                    return false;"><?php echo $lang["page"] ?>&nbsp;<?php echo htmlspecialchars($curpage) ?>&nbsp;<?php echo $lang["of"] ?>&nbsp;<?php echo $totalpages ?></a>
        <?php } ?>

                    |&nbsp;<?php if ($curpage < $totalpages) { ?><a class="nextLink" href="<?php echo $url ?>&go=next&offset=<?php echo urlencode($offset + $per_page) ?>" onClick="return CentralSpaceLoad(this, true);"><?php } ?><?php echo $lang["next"] ?>&nbsp;&gt;<?php if ($curpage < $totalpages) { ?></a><?php } hook("custompagerstyleend"); ?>
                </span>
        <?php if (!$pager_dropdown) { ?>
                        <div id="jumppanel<?php echo $jumpcount ?>" style="display:none;margin-top:5px;"><?php echo $lang["jumptopage"] ?>: <input type="text" size="3" id="jumpto<?php echo $jumpcount ?>" onkeydown="var evt = event || window.event;
                                if (evt.keyCode == 13) {
                                    var jumpto = document.getElementById('jumpto<?php echo $jumpcount ?>').value;
                                    if (jumpto < 1) {
                                        jumpto = 1;
                                    }
                                    ;
                                    if (jumpto ><?php echo $totalpages ?>) {
                                        jumpto =<?php echo $totalpages ?>;
                                    }
                                    ;
                                    CentralSpaceLoad('<?php echo $url ?>&go=page&offset=' + ((jumpto - 1) * <?php echo urlencode($per_page) ?>), true);
                                }">
                            &nbsp;<input type="submit" name="jump" value="<?php echo $lang["jump"] ?>" onClick="var jumpto = document.getElementById('jumpto<?php echo $jumpcount ?>').value;
                                    if (jumpto < 1) {
                                        jumpto = 1;
                                    }
                                    ;
                                    if (jumpto ><?php echo $totalpages ?>) {
                                        jumpto =<?php echo $totalpages ?>;
                                    }
                                    ;
                                    CentralSpaceLoad('<?php echo $url ?>&offset=' + ((jumpto - 1) * <?php echo urlencode($per_page) ?>), true);"></div>
        <?php } ?>
    <?php } else { ?><span class="HorizontalWhiteNav">&nbsp;</span><div <?php if ($pagename == "search") { ?>style="display:block;"<?php } else { ?>style="display:inline;"<?php } ?>>&nbsp;</div><?php } ?>
    <?php
}

function draw_performance_footer()
{
    global $config_show_performance_footer, $querycount, $querytime, $querylog, $pagename, $pageTimer;
    $performance_footer_id = uniqid("performance");
    if ($config_show_performance_footer) {
        $querylog = sortmulti($querylog, "time", "desc", FALSE, FALSE);
        # --- If configured (for debug/development only) show query statistics
        ?>
        <?php if ($pagename == "collections") { ?><br/><br/><br/><br/><br/><br/><br/>
            <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><div style="float:left;"><?php } else { ?><div style="float:right; margin-right: 10px;"><?php } ?>
                <table class="InfoTable" style="float: right;margin-right: 10px;">
                    <tr><td>Page Load</td><td><?php echo $pageTimer->show(); ?> secs</td></tr>
                    <tr><td>Query count</td><td><?php echo $querycount ?></td></tr>
                    <tr><td>Query time</td><td><?php echo round($querytime, 4) ?></td></tr>
        <?php
        $dupes = 0;
        foreach ($querylog as $query => $values) {
            if ($values['dupe'] > 1) {
                $dupes++;
            }
        }
        ?>
                    <tr><td>Dupes</td><td><?php echo $dupes ?></td></tr>
                    <tr><td colspan=2><a href="#" onClick="document.getElementById('querylog<?php echo $performance_footer_id ?>').style.display = 'block';
                            return false;">&gt;&nbsp;details</a></td></tr>
                </table>
                <table class="InfoTable" id="querylog<?php echo $performance_footer_id ?>" style="display: none; float: <?php if ($pagename == 'collections') { ?>left<?php } else { ?>right<?php } ?>; margin: 10px;">
        <?php
        foreach ($querylog as $query => $values) {
            if (substr($query, 0, 7) != "explain" && $query != "show warnings") {
                $show_warnings = false;
                if (strtolower(substr($query, 0, 6)) == "select") {
                    $explain = sql_query("explain extended " . $query);
                    /* $warnings=sql_query("show warnings");
                      $show_warnings=true; */
                }
                ?>
                            <tr><td align="left"><div style="word-wrap: break-word; width:350px;"><?php echo $query ?><?php
                if ($show_warnings) {
                    foreach ($warnings as $warning) {
                        echo "<br /><br />" . $warning['Level'] . ": " . htmlentities($warning['Message']);
                    }
                }
                ?></div></td><td>&nbsp;
                                    <table class="InfoTable">
                <?php if (strtolower(substr($query, 0, 6)) == "select") {
                    ?><tr>
                    <?php foreach ($explain[0] as $explainitem => $value) { ?>
                                                    <td align="left">
                        <?php echo $explainitem ?><br /></td><?php
                    }
                    ?></tr><?php
                    for ($n = 0; $n < count($explain); $n++) {
                        ?><tr><?php foreach ($explain[$n] as $explainitem => $value) { ?>
                                                        <td align="left">
                            <?php echo str_replace(",",
                                    ", ", $value) ?></td><?php
                        }
                        ?></tr><?php
                    }
                }
                ?>
                                    </table>
                                </td><td><?php echo round($values['time'], 4) ?></td>
                            </td><td><?php echo ($values['dupe'] > 1) ? '' . $values["dupe"] . 'X' : '1' ?></td></tr>
                <?php
            }
        }
        ?>
        </table>
        </div>
        <?php
    }
}

// found multidimensional array sort function to support the performance footer
// http://www.php.net/manual/en/function.sort.php#104464
function sortmulti($array, $index, $order, $natsort = FALSE,
        $case_sensitive = FALSE)
{
    if (is_array($array) && count($array) > 0) {
        foreach (array_keys($array) as $key)
            $temp[$key] = $array[$key][$index];
        if (!$natsort) {
            if ($order == 'asc')
                asort($temp);
            else
                arsort($temp);
        }
        else {
            if ($case_sensitive === true)
                natsort($temp);
            else
                natcasesort($temp);
            if ($order != 'asc')
                $temp = array_reverse($temp, TRUE);
        }
        foreach (array_keys($temp) as $key)
            if (is_numeric($key))
                $sorted[] = $array[$key];
            else
                $sorted[$key] = $array[$key];
        return $sorted;
    }
    return $sorted;
}

function format_display_field($value)
{

    // applies trim/wordwrap/highlights

    global $results_title_trim, $results_title_wordwrap, $df, $x, $search;
    $string = i18n_get_translated($value);
    $string = TidyList($string);
    $string = tidy_trim($string, $results_title_trim);
    $wordbreaktag = "<wbr>"; // $wordbreaktag="&#8203;" I'm having slightly better luck with <wbr>, but this pends more testing.
    // Opera doesn't renders the zero-width space with a small box.
    $extra_word_separators = array("_"); // only underscore is necessary (regex considers underscores not to separate words,
    // but we want them to); I've based these transformations on an array just in case more characters act this way.

    $ews_replace = array();
    foreach ($extra_word_separators as $extra_word_separator) {
        $ews_replace[] = "{" . $extra_word_separator . " }";
    }

    //print_r($config_separators_replace);
    $string = str_replace($extra_word_separators, $ews_replace, $string);
    $string = wordwrap($string, $results_title_wordwrap, "#zwspace", false);
    $string = str_replace($ews_replace, $extra_word_separators, $string);
    $string = htmlspecialchars($string);
    $string = highlightkeywords($string, $search, $df[$x]['partial_index'],
            $df[$x]['name'], $df[$x]['indexed']);

    $ews_replace2 = array();
    foreach ($extra_word_separators as $extra_word_separator) {
        $ews_replace2[] = "{" . $extra_word_separator . "#zwspace}";
    }
    $ews_replace3 = array();
    foreach ($extra_word_separators as $extra_word_separator) {
        $ews_replace3[] = $wordbreaktag . $extra_word_separator;
    }

    $string = str_replace($ews_replace2, $ews_replace3, $string);
    $string = str_replace("#zwspace", $wordbreaktag . " ", $string);
    return $string;
}

function error_alert($error, $back = true)
{

    foreach ($GLOBALS as $key => $value) {
        $$key = $value;
    }
    if ($back) {
        include(dirname(__FILE__) . "/header.php");
    }
    echo "<script type='text/javascript'>
	alert('$error');";
    if ($back) {
        echo "history.go(-1);";
    }
    echo "</script>";
}
