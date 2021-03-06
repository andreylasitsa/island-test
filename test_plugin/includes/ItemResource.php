<?php

class ItemResource
{
    function get_items(): WP_REST_Response
    {
        global $wpdb, $table_prefix;
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "items"));

        return new WP_REST_Response($result);
    }

    function add_item(WP_REST_Request $request): WP_REST_Response
    {
        $item_name = (string)$request['name'];
        $item_value = (int)$request['value'];

        global $wpdb, $table_prefix;
        $wpdb->insert($table_prefix . "items", array(
            'name' => addslashes($item_name),
            'value' => $item_value,
        ));

        $last_id = $wpdb->insert_id;

        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_prefix . "items WHERE id = " . $last_id . " ORDER BY id ")
        );

        return new WP_REST_Response($result);
    }

    function get_item(WP_REST_Request $request): WP_REST_Response
    {
        $id_item = (int)$request['id'];
        global $wpdb, $table_prefix;
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "items
        WHERE id = " . $id_item
            )
        );

        return new WP_REST_Response($result);
    }

    function delete_item(WP_REST_Request $request): WP_REST_Response
    {
        $item_id = (int)$request['id'];

        global $wpdb, $table_prefix;
        $wpdb->delete($table_prefix . "items", array(
            'id' => $item_id,
        ));

        return new WP_REST_Response('DELETED');
    }
}