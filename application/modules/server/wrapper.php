<?php
/**
 * server Wrapper
 * Holds server and os-related functions;
 * especially those related to the OS
 *
 * Ideally a Windows version of this wrapper could be dropped in
 * to make this Windows compatible
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/



function run_command($command, $geterrors = false)
{
    # Works like system(), but returns the complete output string rather than just the
    # last line of it.
    global $debug_log;
    debug("CLI command: $command");
    if ($debug_log || $geterrors) {
        $process = @proc_open($command,
                        array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')),
                        $pipe, NULL, NULL, array('bypass_shell' => true));
    } else {
        $process = @proc_open($command, array(1 => array('pipe', 'w')), $pipe,
                        NULL, NULL, array('bypass_shell' => true));
    }
    if (is_resource($process)) {
        $output = trim(stream_get_contents($pipe[1]));
        if ($geterrors) {
            $output.= trim(stream_get_contents($pipe[2]));
        }
        if ($debug_log) {
            debug("CLI output: $output");
            debug("CLI errors: " . trim(stream_get_contents($pipe[2])));
        }
        return $output;
    }

    return '';
}

