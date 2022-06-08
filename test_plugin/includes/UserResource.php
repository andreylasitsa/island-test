<?php

class UserResource
{
    public function get_users(): WP_REST_Response
    {
        global $wpdb, $table_prefix;
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "test_users"));

        return new WP_REST_Response($result);
    }

    public function add_user(WP_REST_Request $request): WP_REST_Response
    {
        $user_name = (string)$request['name'];

        global $wpdb, $table_prefix;
        $wpdb->insert($table_prefix . "test_users", array(
            'name' => addslashes($user_name),
        ));

        $last_id = $wpdb->insert_id;

        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_prefix . "test_users WHERE id = " . $last_id . " ORDER BY id ")
        );

        generateItems($last_id);

        return new WP_REST_Response($result);
    }

    public function delete_user(WP_REST_Request $request): WP_REST_Response
    {
        $user_id = (int)$request['id'];

        global $wpdb, $table_prefix;
        $wpdb->delete($table_prefix . "test_users", array(
            'id' => $user_id,
        ));

        return new WP_REST_Response('DELETED');
    }

    public function edit_user(WP_REST_Request $request): WP_REST_Response
    {
        $user_id = (int)$request['id'];
        $user_name = (string)$request['name'];

        global $wpdb, $table_prefix;
        $result = $wpdb->update($table_prefix . "test_users", array(
            'name' => $user_name
        ), array(
            'id' => $user_id
        ));

        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_prefix . "test_users WHERE id = " . $user_id . " ORDER BY id ")
        );

        return new WP_REST_Response($result);
    }

    public function get_user(WP_REST_Request $request): WP_REST_Response
    {
        $id_user = (int)$request['id'];
        global $wpdb, $table_prefix;
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "test_users
        WHERE id = " . $id_user
            )
        );

        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT " . $table_prefix . "items.name, " . $table_prefix . "items.value FROM 
   `" . $table_prefix . "test_users` INNER JOIN " . $table_prefix . "user_items ON " . $table_prefix . "user_items.id_user = 
   " . $table_prefix . "test_users.id INNER JOIN " . $table_prefix . "items ON " . $table_prefix . "items.id = 
   " . $table_prefix . "user_items.id_item WHERE " . $table_prefix . "test_users.id = " . $id_user
            )
        );

        return new WP_REST_Response($result, $items);
    }

}