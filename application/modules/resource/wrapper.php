<?php

/**
 * resources Wrapper
 * A wrapper round the resource handling functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */


$GLOBALS['get_resource_data_cache'] = array();

function get_resource_data($ref, $cache = true) {
    if ($ref == "") {
        return false;
    }
    # Returns basic resource data (from the resource table alone) for resource $ref.
    # For 'dynamic' field data, see get_resource_field_data
    global $default_resource_type, $get_resource_data_cache, $always_record_resource_creator;
    //  truncate_cache_arrays
    // to prevent cache arrays from going rogue
    // this will prevent long-running scripts from dying as these
    // caches exhaust available memory.
    if (count($GLOBALS['get_resource_data_cache']) > 2000 ) {
        $GLOBALS['get_resource_data_cache'] = array();
        // future improvement: get rid of only oldest, instead of clearing all?
        // this would require a way to guage the age of the entry.
    }

    if ($cache && isset($get_resource_data_cache[$ref])) {
        return $get_resource_data_cache[$ref];
    }
    $resource = sql_query("select *,mapzoom from resource where ref='$ref'");
    if (count($resource) == 0) {
        if ($ref > 0) {
            return false;
        } else {
            # For upload templates (negative reference numbers), generate a new resource if upload permission.
            if (!(checkperm("c") || checkperm("d"))) {
                return false;
            } else {
                if (isset($always_record_resource_creator) && $always_record_resource_creator) {
                    global $userref;
                    $user = $userref;
                } else {
                    $user = -1;
                }
                $wait = sql_query("insert into resource (ref,resource_type,created_by) values ('$ref','$default_resource_type','$user')");
                $resource = sql_query("select *,mapzoom from resource where ref='$ref'");
            }
        }
    }
    $get_resource_data_cache[$ref] = $resource[0];
    return $resource[0];
}


function get_resource_type_field($field) {
    # Returns field data from resource_type_field for the given field.
    $return = sql_query("select * from resource_type_field where ref='$field'");
    if (count($return) == 0) {
        return false;
    } else {
        return $return[0];
    }
}

function get_resource_field_data($ref, $multi = false, $use_permissions = true, $originalref = -1, $external_access = false, $ord_by = false) {
    # Returns field data and field properties (resource_type_field and resource_data tables)
    # for this resource, for display in an edit / view form.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.
    # Find the resource type.
    if ($originalref == -1) {
        $originalref = $ref;
    } # When a template has been selected, only show fields for the type of the original resource ref, not the template (which shows fields for all types)
    $rtype = sql_value("select resource_type value from resource where ref='$originalref'", 0);

    # If using metadata templates,
    $templatesql = "";
    global $metadata_template_resource_type;
    if (isset($metadata_template_resource_type) && $metadata_template_resource_type == $rtype) {
        # Show all resource fields, just as with editing multiple resources.
        $multi = true;
    }

    $return = array();
    if ($ord_by) {
        $fields = sql_query("select d.value,d.resource_type_field,f.exiftool_field,f.value_filter,f.name,f.display_template,f.display_field,f.tab_name,f.options,f.keywords_index,f.resource_column,f.required,f.type,f.title,f.resource_type,f.required frequired,f.ref,f.ref fref, f.help_text,f.partial_index,f.external_user_access,f.hide_when_uploading,f.hide_when_restricted,f.omit_when_copying,f.regexp_filter,f.display_condition from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where ( " . (($multi) ? "1=1" : "f.resource_type=0 or f.resource_type=999 or f.resource_type='$rtype'") . ") order by f.order_by,f.resource_type,f.ref");
    } else {
        $fields = sql_query("select d.value,d.resource_type_field,f.exiftool_field,f.value_filter,f.name,f.display_template,f.display_field,f.tab_name,f.options,f.keywords_index,f.resource_column,f.required,f.type,f.title,f.resource_type,f.required frequired,f.ref, f.ref fref, f.help_text,f.partial_index,f.external_user_access,f.hide_when_uploading,f.hide_when_restricted,f.omit_when_copying,f.regexp_filter,f.display_condition from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where ( " . (($multi) ? "1=1" : "f.resource_type=0 or f.resource_type=999 or f.resource_type='$rtype'") . ") order by f.resource_type,f.order_by,f.ref");
        debug("altert use perms: " . !$use_permissions);
    }

    # Build an array of valid types and only return fields of this type. Translate field titles.
    $validtypes = sql_array("select ref value from resource_type");
    $validtypes[] = 0;
    $validtypes[] = 999; # Support archive and global.
    for ($n = 0; $n < count($fields); $n++) {
        if
        (
                (
                !$use_permissions ||
                (
                # Upload only edit access to this field?
                $ref < 0 && checkperm("P" . $fields[$n]["fref"])
                ) ||
                (
                (
                checkperm("f*") || checkperm("f" . $fields[$n]["fref"])
                ) && !checkperm("f-" . $fields[$n]["fref"]) && !checkperm("T" . $fields[$n]["resource_type"])
                )
                ) && in_array($fields[$n]["resource_type"], $validtypes) &&
                (
                !
                (
                $external_access && !$fields[$n]["external_user_access"]
                )
                )
        ) {
            debug("field" . $fields[$n]["title"] . "=" . $fields[$n]["value"]);
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"], "fieldtitle-");
            $return[] = $fields[$n];
        }
    }
    return $return;
}

function get_resource_field_data_batch($refs) {
    # Returns field data and field properties (resource_type_field and resource_data tables)
    # for all the resource references in the array $refs.
    # This will use a single SQL query and is therefore a much more efficient way of gathering
    # resource data for a list of resources (e.g. search result display for a page of resources).
    if (count($refs) == 0) {
        return array();
    } # return an empty array if no resources specified (for empty result sets)
    $refsin = join(",", $refs);
    $results = sql_query("select d.resource,f.*,d.value from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource in ($refsin) where (f.resource_type=0 or f.resource_type in (select resource_type from resource where ref=d.resource)) order by d.resource,f.order_by,f.ref");
    $return = array();
    $res = 0;
    for ($n = 0; $n < count($results); $n++) {
        if ($results[$n]["resource"] != $res) {
            # moved on to the next resource
            if ($res != 0) {
                $return[$res] = $resdata;
            }
            $resdata = array();
            $res = $results[$n]["resource"];
        }
        # copy name/value into resdata array
        $resdata[$results[$n]["ref"]] = $results[$n];
    }
    $return[$res] = $resdata;
    return $return;
}

function get_resource_types($types = "", $translate = true) {
    # Returns a list of resource types. The standard resource types are translated using $lang. Custom resource types are i18n translated.
    // support getting info for a comma-delimited list of restypes (as in a search)
    if ($types == "") {
        $sql = "";
    } else {
        # Ensure $types are suitably quoted and escaped
        $cleantypes = "";
        $s = explode(",", $types);
        foreach ($s as $type) {
            if (is_numeric(str_replace("'", "", $type))) { # Process numeric types only, to avoid inclusion of collection-based filters (mycol, public, etc.)
                if (strpos($type, "'") === false) {
                    $type = "'" . $type . "'";
                }
                if ($cleantypes != "") {
                    $cleantypes.=",";
                }
                $cleantypes.=$type;
            }
        }
        $sql = " where ref in ($cleantypes) ";
    }

    $r = sql_query("select * from resource_type $sql order by order_by,ref");
    $return = array();
    # Translate names (if $translate==true) and check permissions
    for ($n = 0; $n < count($r); $n++) {
        if (!checkperm('T' . $r[$n]['ref'])) {
            if ($translate == true) {
                $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"], "resourcetype-");
            } # Translate name
            $return[] = $r[$n]; # Add to return array
        }
    }
    return $return;
}

function get_resource_top_keywords($resource, $count) {
    # Return the top $count keywords (by hitcount) used by $resource.
    # This is for the 'Find Similar' search.
    # Keywords that are too short or too long, or contain numbers are dropped - they are probably not as meaningful in
    # the contexts of this search (consider being offered "12" or "OKB-34" as an option?)
    $return = array();
    $keywords = sql_query("select distinct k.ref,k.keyword keyword,f.ref field,f.resource_type from keyword k,resource_keyword r,resource_type_field f where k.ref=r.keyword and r.resource='$resource' and f.ref=r.resource_type_field and f.use_for_similar=1 and length(k.keyword)>=3 and length(k.keyword)<=15 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' order by k.hit_count desc limit $count");
    foreach ($keywords as $keyword) {
        # Apply permissions and strip out any results the user does not have access to.
        if ((checkperm("f*") || checkperm("f" . $keyword["field"])) && !checkperm("f-" . $keyword["field"]) && !checkperm("T" . $keyword["resource_type"])) {
            # Has access to this field.
            $return[] = $keyword["keyword"];
        }
    }
    return $return;
}

if (!function_exists("split_keywords")) {

    function split_keywords($search, $index = false, $partial_index = false, $is_date = false, $is_html = false) {
        # Takes $search and returns an array of individual keywords.
        global $config_trimchars, $daterange_search;

        if ($index && $is_date) {
            # Date handling... index a little differently to support various levels of date matching (Year, Year+Month, Year+Month+Day).
            $s = explode("-", $search);
            if (count($s) >= 3) {
                return (array($s[0], $s[0] . "-" . $s[1], $search));
            } else {
                return $search;
            }
        }


        # Remove any real / unescaped lf/cr
        $search = str_replace("\r", " ", $search);
        $search = str_replace("\n", " ", $search);
        $search = str_replace("\\r", " ", $search);
        $search = str_replace("\\n", " ", $search);

        $ns = trim_spaces($search);

        if ((substr($ns, 0, 1) == ",") || ($index == false && strpos($ns, ":") !== false)) { # special 'constructed' query type, split using comma so
        # we support keywords with spaces.
            if ((strpos($ns, "startdate") == false && strpos($ns, "enddate") == false && strpos($ns, "range") == false) || (!$daterange_search)) {
                $ns = cleanse_string($ns, true, !$index, $is_html);
            }
            $return = explode(",", $ns);
            # If we are indexing, append any values that contain spaces.
            # Important! Solves the searching for keywords with spaces issue.
            # Consider: for any keyword that has spaces, append to the array each individual word too
            # so for example: South Asia,USA becomes South Asia,USA,South,Asia
            # so a plain search for 'south asia' will match those with the keyword 'south asia' because the resource
            # will also be linked to the words 'south' and 'asia'.
            if ($index) {
                $return2 = $return;
                for ($n = 0; $n < count($return); $n++) {
                    $keyword = trim($return[$n]);
                    if (strpos($keyword, " ") !== false) {
                        # append each word
                        $words = explode(" ", $keyword);
                        for ($m = 0; $m < count($words); $m++) {
                            $return2[] = trim($words[$m]);
                        }
                    }
                }
                $return2 = trim_array($return2, $config_trimchars);
                if ($partial_index) {
                    return add_partial_index($return2);
                }
                return $return2;
            } else {
                return trim_array($return, $config_trimchars);
            }
        } else {
            # split using spaces and similar chars (according to configured whitespace characters)
            if (strpos($ns, "range") === false) {
                $ns = explode(" ", cleanse_string($ns, false, !$index, $is_html));
            } else {
                $ns = explode(" ", cleanse_string($ns, true, true));
            }
            $ns = trim_array($ns, $config_trimchars);
            if ($index && $partial_index) {
                return add_partial_index($ns);
            }
            return $ns;
        }
    }

}

if (!function_exists("cleanse_string")) {

    function cleanse_string($string, $preserve_separators, $preserve_hyphen = false, $is_html = false) {
        # Removes characters from a string prior to keyword splitting, for example full stops
        # Also makes the string lower case ready for indexing.
        global $config_separators;
        $separators = $config_separators;

        if ($is_html) {
            $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        }

        if ($preserve_hyphen) {
            # Preserve hyphen - used when NOT indexing so we know which keywords to omit from the search.
            if ((substr($string, 0, 1) == "-" /* support minus as first character for simple NOT searches */ || strpos($string, " -") !== false) && strpos($string, " - ") == false) {
                $separators = array_diff($separators, array("-")); # Remove hyphen from separator array.
            }
        }
        if ($preserve_separators) {
            return mb_strtolower(trim_spaces(str_replace($separators, " ", $string)), 'UTF-8');
        } else {
            # Also strip out the separators used when specifying multiple field/keyword pairs (comma and colon)
            $s = $separators;
            $s[] = ",";
            $s[] = ":";
            return mb_strtolower(trim_spaces(str_replace($s, " ", $string)), 'UTF-8');
        }
    }

}

if (!function_exists("resolve_keyword")) {

    function resolve_keyword($keyword, $create = false) {
        # Returns the keyword reference for $keyword, or false if no such keyword exists.
        $return = sql_value("select ref value from keyword where keyword='" . trim(escape_check($keyword)) . "'", false);
        if ($return === false && $create) {
            # Create a new keyword.
            sql_query("insert into keyword (keyword,soundex,hit_count) values ('" . escape_check($keyword) . "',soundex('" . escape_check($keyword) . "'),0)");
            $return = sql_insert_id();
        }
        return $return;
    }

}

function add_partial_index($keywords) {
    # For each keywords in the supplied keywords list add all possible infixes and return the combined array.
    # This therefore returns all keywords that need indexing for the given string.
    # Only for fields with 'partial_index' enabled.
    $return = array();
    $position = 0;
    $x = 0;
    for ($n = 0; $n < count($keywords); $n++) {
        $keyword = trim($keywords[$n]);
        $return[$x]['keyword'] = $keyword;
        $return[$x]['position'] = $position;
        $x++;
        if (strpos($keyword, " ") === false) { # Do not do this for keywords containing spaces as these have already been broken to individual words using the code above.
            global $partial_index_min_word_length;
            # For each appropriate infix length
            for ($m = $partial_index_min_word_length; $m < strlen($keyword); $m++) {
                # For each position an infix of this length can exist in the string
                for ($o = 0; $o <= strlen($keyword) - $m; $o++) {
                    $infix = substr($keyword, $o, $m);
                    $return[$x]['keyword'] = $infix;
                    $return[$x]['position'] = $position; // infix has same position as root
                    $x++;
                }
            }
        } # End of no-spaces condition
        $position++; // end of root keyword
    } # End of partial indexing keywords loop
    return $return;
}


function update_resource_keyword_hitcount($resource, $search) {
    # For the specified $resource, increment the hitcount for each matching keyword in $search
    # This is done into a temporary column first (new_hit_count) so existing results are not affected.
    # copy_hitcount_to_live() is then executed at a set interval to make this data live.
    $keywords = split_keywords($search);
    $keys = array();
    for ($n = 0; $n < count($keywords); $n++) {
        $keyword = $keywords[$n];
        if (strpos($keyword, ":") !== false) {
            $k = explode(":", $keyword);
            $keyword = $k[1];
        }
        $found = resolve_keyword($keyword);
        if ($found !== false) {
            $keys[] = resolve_keyword($keyword);
        }
    }
    if (count($keys) > 0) {
        sql_query("update resource_keyword set new_hit_count=new_hit_count+1 where resource='$resource' and keyword in (" . join(",", $keys) . ")");
    }
}

function copy_hitcount_to_live() {
    # Copy the temporary hit count used for relevance matching to the live column so it's activated (see comment for
    # update_resource_keyword_hitcount())
    sql_query("update resource_keyword set hit_count=new_hit_count");

    # Also update the resource table
    # greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability)
    sql_query("update resource set hit_count=greatest(hit_count,new_hit_count)");
}


function get_related_resources($ref) {
    # Return an array of resource references that are related to resource $ref
    return sql_array("select related value from resource_related where resource='$ref' union select resource value from resource_related where related='$ref'");
}

function get_field_options($ref) {
    # For the field with reference $ref, return a sorted array of options.
    $options = sql_value("select options value from resource_type_field where ref='$ref'", "");

    # Translate all options
    $options = trim_array(explode(",", $options));
    for ($m = 0; $m < count($options); $m++) {
        $options[$m] = i18n_get_translated($options[$m]);
    }

    global $auto_order_checkbox;
    if ($auto_order_checkbox) {
        sort($options);
    }

    return $options;
}

function get_data_by_field($resource, $field) {
    # Return the resource data for field $field in resource $resource
    # $field can also be a shortname
    if (is_numeric($field)) {
        return sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'", "");
    } else {
        return sql_value("select value from resource_data where resource='$resource' and resource_type_field=(select ref from resource_type_field where name='" . escape_check($field) . "')", "");
    }
}


function highlightkeywords($text, $search, $partial_index = false, $field_name = "", $keywords_index = 1) {
    # do not hightlight if the field is not indexed, so it is clearer where results came from.
    if ($keywords_index != 1) {
        return $text;
    }

    # Highlight searched keywords in $text
    # Optional - depends on $highlightkeywords being set in config.php.
    global $highlightkeywords;
    # Situations where we do not need to do this.
    if (!isset($highlightkeywords) || ($highlightkeywords == false) || ($search == "") || ($text == "")) {
        return $text;
    }


    # Generate the cache of search keywords (no longer global so it can test against particular fields.
    # a search is a small array so I don't think there is much to lose by processing it.
    $hlkeycache = array();
    $wildcards_found = false;
    $s = split_keywords($search);
    for ($n = 0; $n < count($s); $n++) {
        if (strpos($s[$n], ":") !== false) {
            $c = explode(":", $s[$n]);
            # only add field specific keywords
            if ($field_name != "" && $c[0] == $field_name) {
                $hlkeycache[] = $c[1];
            }
        }
        # else add general keywords
        else {
            $keyword = $s[$n];
            if (strpos($keyword, "*") !== false) {
                $wildcards_found = true;
                $keyword = str_replace("*", "", $keyword);
            }
            $hlkeycache[] = $keyword;
        }
    }

    # Parse and replace.
    if ($partial_index || $wildcards_found) {
        return str_highlight($text, $hlkeycache, STR_HIGHLIGHT_SIMPLE);
    } else {
        return str_highlight($text, $hlkeycache, STR_HIGHLIGHT_WHOLEWD);
    }
}


# These lines go with str_highlight (next).
define('STR_HIGHLIGHT_SIMPLE', 1);
define('STR_HIGHLIGHT_WHOLEWD', 2);
define('STR_HIGHLIGHT_CASESENS', 4);
define('STR_HIGHLIGHT_STRIPLINKS', 8);

function str_highlight($text, $needle, $options = null, $highlight = null) {
    # Thanks to Aidan Lister <aidan@php.net>
    # Sourced from http://aidanlister.com/repos/v/function.str_highlight.php on 2007-10-09
    # License on the website reads: "All code on this website resides in the Public Domain, you are free to use and modify it however you wish."
    # http://aidanlister.com/repos/license/

    $text = str_replace("_", ".{us}.", $text); // underscores are considered part of words, so temporarily replace them for better \b search.
    $text = str_replace("#zwspace;", ".{zw}.", $text);

    // Default highlighting
    if ($highlight === null) {
        $highlight = '<span class="highlight">\1</span>';
    }

    // Select pattern to use
    if ($options & STR_HIGHLIGHT_SIMPLE) {
        $pattern = '#(%s)#';
        $sl_pattern = '#(%s)#';
    } else {
        $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
        $sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';
    }

    // Case sensitivity
    if (!($options & STR_HIGHLIGHT_CASESENS)) {
        $pattern .= 'i';
        $sl_pattern .= 'i';
    }

    $needle = (array) $needle;

    usort($needle, "sorthighlights");

    foreach ($needle as $needle_s) {
        if (strlen($needle_s) > 0) {
            $needle_s = preg_quote($needle_s);
            $needle_s = str_replace("#", "\\#", $needle_s);

            // Escape needle with optional whole word check
            if ($options & STR_HIGHLIGHT_WHOLEWD) {
                $needle_s = '\b' . $needle_s . '\b';
            }

            // Strip links
            if ($options & STR_HIGHLIGHT_STRIPLINKS) {
                $sl_regex = sprintf($sl_pattern, $needle_s);
                $text = preg_replace($sl_regex, '\1', $text);
            }

            $regex = sprintf($pattern, $needle_s);
            $text = preg_replace($regex, $highlight, $text);
        }
    }
    $text = str_replace(".{us}.", "_", $text);
    $text = str_replace(".{zw}.", "#zwspace;", $text);
    return $text;
}

function sorthighlights($a, $b) {
    # fixes an odd problem for str_highlight related to the order of keywords
    if (strlen($a) < strlen($b)) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

function get_suggested_keywords($search, $ref = "")
{
    # For the given partial word, suggest complete existing keywords.
    global $autocomplete_search_items, $autocomplete_search_min_hitcount;
    if ($ref == "") {
        return sql_array("select distinct keyword value from keyword where keyword like '" . escape_check($search) . "%' and hit_count >= '$autocomplete_search_min_hitcount' order by hit_count desc limit $autocomplete_search_items");
    } else {
        return sql_array("select distinct k.keyword value,rk.resource_type_field from keyword k,resource_keyword rk where k.ref=rk.keyword and k.keyword like '" . escape_check($search) . "%' and rk.resource_type_field='" . $ref . "' and k.hit_count >= '$autocomplete_search_min_hitcount' order by k.hit_count desc limit $autocomplete_search_items");
    }
}

function get_related_keywords($keyref)
{
    # For a given keyword reference returns the related keywords
    # Also reverses the process, returning keywords for matching related words
    # and for matching related words, also returns other words related to the same keyword.
    global $keyword_relationships_one_way;
    global $related_keywords_cache;
    if (isset($related_keywords_cache[$keyref])) {
        return $related_keywords_cache[$keyref];
    } else {
        if ($keyword_relationships_one_way) {
            $related_keywords_cache[$keyref] = sql_array(" select related value from keyword_related where keyword='$keyref'");
            return $related_keywords_cache[$keyref];
        } else {
            $related_keywords_cache[$keyref] = sql_array(" select keyword value from keyword_related where related='$keyref' union select related value from keyword_related where (keyword='$keyref' or keyword in (select keyword value from keyword_related where related='$keyref')) and related<>'$keyref'");
            return $related_keywords_cache[$keyref];
        }
    }
}

function get_grouped_related_keywords($find = "", $specific = "")
{
    # Returns each keyword and the related keywords grouped, along with the resolved keywords strings.
    $sql = "";
    if ($find != "") {
        $sql = "where k1.keyword='" . escape_check($find) . "' or k2.keyword='" . escape_check($find) . "'";
    }
    if ($specific != "") {
        $sql = "where k1.keyword='" . escape_check($specific) . "'";
    }

    return sql_query("
		select k1.keyword,group_concat(k2.keyword order by k2.keyword separator ', ') related from keyword_related kr
			join keyword k1 on kr.keyword=k1.ref
			join keyword k2 on kr.related=k2.ref
		$sql
		group by k1.keyword order by k1.keyword
		");
}

function save_related_keywords($keyword, $related)
{
    $keyref = resolve_keyword($keyword, true);
    $s = trim_array(explode(",", $related));

    # Blank existing relationships.
    sql_query("delete from keyword_related where keyword='$keyref'");
    if (trim($related) != "") {
        for ($n = 0; $n < count($s); $n++) {
            sql_query("insert into keyword_related (keyword,related) values ('$keyref','" . resolve_keyword($s[$n],
                            true) . "')");
        }
    }
    return true;
}

function get_simple_search_fields()
{
    # Returns a list of fields suitable for the simple search box.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    $sql = "";

    # Include the country field even if not selected?
    # This is to provide compatibility for older systems on which the simple search box was not configurable
    # and had a simpler 'country search' option.
    global $country_search;
    if (isset($country_search) && $country_search) {
        $sql = " or ref=3";
    }

    # Executes query.
    $fields = sql_query("select ref, name, title, type, options, order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown, external_user_access, autocomplete_macro, hide_when_uploading, hide_when_restricted, value_filter, exiftool_filter, omit_when_copying, tooltip_text from resource_type_field where (simple_search=1 $sql) and keywords_index=1 order by resource_type,order_by");

    # Applies field permissions and translates field titles in the newly created array.
    $return = array();
    for ($n = 0; $n < count($fields); $n++) {
        if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"])) && !checkperm("f-" . $fields[$n]["ref"])) {
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"],
                    "fieldtitle-");
            $return[] = $fields[$n];
        }
    }
    return $return;
}


function get_fields($field_refs)
{
    # Returns a list of fields with refs matching the supplied field refs.
    if (!is_array($field_refs)) {
        print_r($field_refs);
        exit(" passed to getfields() is not an array. ");
    }
    $return = array();
    $fields = sql_query("select ref, name, title, type, options ,order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown,tooltip_text,display_condition from resource_type_field where  keywords_index=1 and length(name)>0 and ref in ('" . join("','",
                    $field_refs) . "') order by order_by");
    # Apply field permissions
    for ($n = 0; $n < count($fields); $n++) {
        if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"])) && !checkperm("f-" . $fields[$n]["ref"])) {
            $return[] = $fields[$n];
        }
    }
    return $return;
}

function get_hidden_indexed_fields()
{
    # Return an array of indexed fields to which the current user does not have access
    # Used by do_search to ommit fields when searching.
    $hidden = array();
    global $hidden_fields_cache;
    if (is_array($hidden_fields_cache)) {
        return $hidden_fields_cache;
    } else {
        $fields = sql_query("select ref from resource_type_field where keywords_index=1 and length(name)>0");
        # Apply field permissions
        for ($n = 0; $n < count($fields); $n++) {
            if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"])) && !checkperm("f-" . $fields[$n]["ref"])) {
                # Visible field
            } else {
                # Hidden field
                $hidden[] = $fields[$n]["ref"];
            }
        }
        $hidden_fields_cache = $hidden;
        return $hidden;
    }
}

function get_category_tree_fields()
{
    # Returns a list of fields with refs matching the supplied field refs.
    global $cattreefields_cache;
    if (is_array($cattreefields_cache)) {
        return $cattreefields_cache;
    } else {
        $fields = sql_query("select name from resource_type_field where type=7 and length(name)>0 order by order_by");
        $cattreefields = array();
        foreach ($fields as $field) {
            $cattreefields[] = $field['name'];
        }
        $cattreefields_cache = $cattreefields;
        return $cattreefields;
    }
}

function get_OR_fields()
{
    # Returns a list of fields that should retain semicolon separation of keywords in a search string
    global $orfields_cache;
    if (is_array($orfields_cache)) {
        return $orfields_cache;
    } else {
        $fields = sql_query("select name from resource_type_field where type=7 or type=2 or type=3 and length(name)>0 order by order_by");
        $orfields = array();
        foreach ($fields as $field) {
            $orfields[] = $field['name'];
        }
        $orfields_cache = $orfields;
        return $orfields;
    }
}

function get_fields_for_search_display($field_refs)
{
    # Returns a list of fields/properties with refs matching the supplied field refs, for search display setup
    # This returns fewer columns and doesn't require that the fields be indexed, as in this case it's only used to judge whether the field should be highlighted.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    if (!is_array($field_refs)) {
        print_r($field_refs);
        exit(" passed to getfields() is not an array. ");
    }

    # Executes query.
    $fields = sql_query("select ref, name, type, title, keywords_index, partial_index, value_filter from resource_type_field where ref in ('" . join("','",
                    $field_refs) . "')");

    # Applies field permissions and translates field titles in the newly created array.
    $return = array();
    for ($n = 0; $n < count($fields); $n++) {
        if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"])) && !checkperm("f-" . $fields[$n]["ref"])) {
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"],
                    "fieldtitle-");
            $return[] = $fields[$n];
        }
    }
    return $return;
}

function image_size_restricted_access($id)
{
    # Returns true if the indicated size is allowed for a restricted user.
    return sql_value("select allow_restricted value from preview_size where id='$id'",
            false);
}

function strip_leading_comma($val)
{
    # make sure value is numeric if it can be, i.e. for ratings
    # not sure if it's ok to remove commas before any value, since they were explicitly added
    if (is_numeric(str_replace(",", "", $val))) {
        $val = str_replace(",", "", $val);
    }
    return $val;
}


function purchase_set_size($collection, $resource, $size, $price)
{
    // Set the selected size for an item in a collection. This is used later on when the items are downloaded.
    sql_query("update collection_resource set purchase_size='" . escape_check($size) . "',purchase_price='" . escape_check($price) . "' where collection='$collection' and resource='$resource'");
    return true;
}

function payment_set_complete($collection)
{
    global $applicationname, $baseurl, $userref, $username, $useremail, $userfullname, $email_notify, $lang, $currency_symbol;
    # Mark items in the collection as paid so they can be downloaded.
    sql_query("update collection_resource set purchase_complete=1 where collection='$collection'");

    # For each resource, add an entry to the log to show it has been purchased.
    $resources = sql_query("select * from collection_resource where collection='$collection'");
    $summary = "<style>.InfoTable td {padding:5px;}</style><table border=\"1\" class=\"InfoTable\"><tr><td><strong>" . $lang["property-reference"] . "</strong></td><td><strong>" . $lang["size"] . "</strong></td><td><strong>" . $lang["price"] . "</strong></td></tr>";
    foreach ($resources as $resource) {
        $purchasesize = $resource["purchase_size"];
        if ($purchasesize == "") {
            $purchasesize = $lang["original"];
        }
        resource_log($resource["resource"], "p", 0, "", "", "", 0,
                $resource["purchase_size"], $resource["purchase_price"]);
        $summary.="<tr><td>" . $resource["resource"] . "</td><td>" . $purchasesize . "</td><td>" . $currency_symbol . $resource["purchase_price"] . "</td></tr>";
    }
    $summary.="</table>";
    # Send email to admin
    $message = $lang["purchase_complete_email_admin_body"] . "<br>" . $lang["username"] . ": " . $username . "(" . $userfullname . ")<br>" . $summary . "<br><br>$baseurl/?c=" . $collection . "<br>";
    send_mail($email_notify,
            $applicationname . ": " . $lang["purchase_complete_email_admin"],
            $message, "", "", "", null, "", "", true);

    #Send email to user
    $userconfirmmessage = $lang["purchase_complete_email_user_body"] . $summary . "<br><br>$baseurl/?c=" . $collection . "<br>";
    send_mail($useremail,
            $applicationname . ": " . $lang["purchase_complete_email_user"],
            $userconfirmmessage, "", "", "", null, "", "", true);

    # Rename so that can be viewed on my purchases page
    sql_query("update collection set name= '" . date("Y-m-d H:i") . "' where ref='$collection'");

    return true;
}

function get_all_image_sizes($internal = false, $restricted = false)
{
    # Returns all image sizes available.
    # Standard image sizes are translated using $lang.  Custom image sizes are i18n translated.
    # Executes query.
    $r = sql_query("select * from preview_size " . (($internal) ? "" : "where internal!=1") . (($restricted) ? " and allow_restricted=1" : "") . " order by width asc");

    # Translates image sizes in the newly created array.
    $return = array();
    for ($n = 0; $n < count($r); $n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"],
                "imagesize-");
        $return[] = $r[$n];
    }
    return $return;
}

