<?php
/**
 * Created by PhpStorm.
 * User: Sshucchi
 * Date: 01/05/2017
 * Time: 13:19
 */

require_once 'amplitude-php/vendor/autoload.php';
use \Zumba\Amplitude\Amplitude as Amplitude;

class q2a_amplitude_event{

    function process_event($event, $userid, $handle, $cookieid, $params)
    {
        $amplitude = new Amplitude();
        $userEmail = qa_is_logged_in()?qa_get_logged_in_email():$cookieid;
        $userProperties = array(
            'userName' =>$handle
        );
        $amplitude->init(qa_opt('amplitude_key'), $userEmail)
                  ->setUserProperties($userProperties);
        $eventProperties = array();

        switch($event){
            case 'u_logout':
            case 'u_login':
                $eventProperties['referer'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
                break;

            case 'u_confirmed':
                $amplitude->setUserProperties(['verified'=>true]);
                break;

            case 'u_register':
                $userProperties['verified']=false;
                $amplitude->setUserProperties($userProperties);
                $eventProperties['referer'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
                break;

            case 'q_post':
            case 'c_post':
                $eventProperties['size'] = strlen($params['text']);
                break;
            case 'a_post':
                $eventProperties['size'] = strlen($params['text']);
                $amplitude->setUserId($params['parent']['email']);
                if(qa_get_logged_in_level()>=QA_USER_LEVEL_EXPERT)
                    $eventName = qa_lang_html('plugin_amplitude_tagging/received_answer_from_expert');
                else
                    $eventName = qa_lang_html('plugin_amplitude_tagging/received_answer');
                $amplitude->logEvent($eventName,$eventProperties);
                $amplitude->setUserId($userEmail);
                break;
            case 'q_edit':
            case 'a_edit':
            case 'c_edit':
                $eventProperties['newsize'] = strlen($params['content']);
                $eventProperties['oldsize'] = strlen($params['oldcontent']);
                break;
            case 'a_select':
            case 'a_unselect':
                $eventProperties['question'] = $params['parentid'];
                break;
            case 'search':
                $eventProperties['query_length'] = strlen($params['query']);
                $eventProperties['query'] = $params['query'];
                $eventProperties['referer'] = isset($_SERVER['HTTP_REFERER'])?parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH):'direct';
                break;
        }

            if(!empty($eventProperties))
                $amplitude->logEvent(qa_lang_html('plugin_amplitude_tagging/'.$event),$eventProperties);


    }
    function admin_form(&$qa_content)
    {

        // Process form input

        $ok = null;

        if (qa_clicked('amplitude_save')) {
            qa_opt('amplitude_key', qa_post_text('amplitude_key'));

            $ok = qa_lang('admin/options_saved');
        }


        // Create the form for display


        $fields = array();

        $fields[] = array(
            'label' => 'Amplitude API key',
            'tags' => 'NAME="amplitude_key"',
            'value' => qa_opt('amplitude_key'),
            'type' => 'text');



        return array(
            'ok' => ($ok && !isset($error)) ? $ok : null,

            'fields' => $fields,

            'buttons' => array(
                array(
                    'label' => qa_lang_html('main/save_button'),
                    'tags' => 'NAME="amplitude_save"',
                ),
            ),
        );
    }
}