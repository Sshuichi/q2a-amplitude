<?php
/**
 * Created by PhpStorm.
 * User: Sshucchi
 * Date: 01/05/2017
 * Time: 13:19
 */
/*
  Plugin Name: Amplitude event tracking plugin
  Plugin URI: https://github.com/Sshuichi/q2a-amplitude
  Plugin Description: Tracks Mahkamaty events and send them to Amplitude.
  Plugin Version: 1.0
  Plugin Date: 2018-01-01
  Plugin Author: Sshuicchi
  Plugin Author URI:
  Plugin License: GPLv2
  Plugin Minimum Question2Answer Version: 1.7
  Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}
qa_register_plugin_module('event', 'q2a-amplitude-event.php','q2a_amplitude_event','Tag events and send them to Amplitude');
qa_register_plugin_phrases(
    'q2a-amplitude-event-lang-*.php', // pattern for language files
    'plugin_amplitude_tagging' // prefix to retrieve phrases
);
qa_register_plugin_layer(
    'q2a-amplitude-layer.php', // pattern for language files
    'qa_html_theme_layer' // prefix to retrieve phrases
);