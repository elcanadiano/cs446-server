<?php

/**
 * UWwaterloo Open Data API
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author	Khalim Husain
 */

class Open_data_api  {
	function get_course($subject, $course_catalog) {
        // Construct the URL.
        $url = config_item('open_data_base_url') . '/courses/' . $subject . '/' . $course_catalog . '.json?key=' . config_item('open_data_key');

        $contents = file_get_contents($url);

        $json = json_decode($contents);

        // If there was an error.
        if (!isset($json->data) || !(array)($json->data))
        {
            return NULL;
        }

        $data = $json->data;

        $ret = array(
            'course_id' => $data->course_id,
            'subject' => $data->subject,
            'catalog_number' => $data->catalog_number,
            'title' => $data->title
        );

        return $ret;
	}

}
