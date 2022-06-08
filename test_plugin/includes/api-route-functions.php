<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require('BidResource.php');
require('UserResource.php');
require('ItemResource.php');

/**
 * @param $result
 * @param $consumer_id
 * @param $consumer_items
 * @param $creator_id
 * @param $creator_items
 * @param wpdb $wpdb
 * @param string $table_prefix
 * @param int $lot_id
 * @return array
 */
function extracted($result, $consumer_id, $consumer_items, $creator_id, $creator_items, wpdb $wpdb, string $table_prefix, int $lot_id): WP_REST_Response
{
    if (!empty($result)) {
        if (!checkUser($consumer_id) || !checkUserItems($consumer_id, $consumer_items)) {
            return new WP_REST_Response("please check consumer items", 422);
        }

        if (!checkUser($creator_id) || !checkUserItems($creator_id, $creator_items)) {
            return new WP_REST_Response("please check creator items", 422);
        }
    }

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "items"));

    $consumer_items_id = [];
    $creator_items_id = [];

    foreach ($consumer_items->items as $consumer_item) {
        foreach ($result as $res) {
            if ($consumer_item === $res->name) {
                $consumer_items_id[] = $res->id;
            }
        }
    }

    foreach ($consumer_items_id as $consumer_item) {
        $wpdb->get_results($wpdb->prepare("UPDATE `" . $table_prefix . "user_items` SET id_user = " . $creator_id . " WHERE id_item = " . $consumer_item . " and id_user = " . $consumer_id . " LIMIT 1"));
    }

    foreach ($creator_items->items as $creator_item) {
        foreach ($result as $res) {
            if ($creator_item === $res->name) {
                $creator_items_id[] = $res->id;
            }
        }
    }

    foreach ($creator_items_id as $creator_item) {
        $result = $wpdb->get_results($wpdb->prepare("UPDATE `" . $table_prefix . "user_items` SET id_user = " . $consumer_id . " WHERE id_item = " . $creator_item . " and id_user = " . $creator_id . " LIMIT 1"));
    }

    $wpdb->update($table_prefix . "lots", array(
        'status' => 'close',
        'consumer_id' => $consumer_id
    ), array(
        'id' => $lot_id
    ));

    return new WP_REST_Response("deal is successful");
}