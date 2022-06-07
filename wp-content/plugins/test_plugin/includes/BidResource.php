<?php

class BidResource
{
    function add_lot(WP_REST_Request $request): array
    {
        global $wpdb, $table_prefix;
        $creator_id = (int)$request['creator_id'];
        $consumer_id = (int)$request['consumer_id'];
        $creator_items = (string)$request['creator_items'];
        $consumer_items = (string)$request['consumer_items'];

        if ($creator_id === $consumer_id || $creator_id === 0) {
            return returnResult("Check consumer_id and creator_id", 422);
        }

        if (!checkUser($creator_id)) {
            return returnResult("User " . $creator_id . " doesn't exist", 422);
        }
        if ($consumer_id != 0) {
            if (!checkUser($consumer_id)) {
                return returnResult("User " . $creator_id . " doesn't exist", 422);
            }
        }

        $creator_items = json_decode($creator_items);
        $consumer_items = json_decode($consumer_items);

        if (!checkUserItems($creator_id, $creator_items)) {
            return returnResult("Check creator items, please", 422);
        }

        if ($consumer_id != 0) {
            if (!checkUserItems($creator_id, $consumer_items)) {
                return returnResult("Check consumer items, please", 422);
            }
        } else {
            if (!checkTotal($consumer_items)) {
                return returnResult("Check consumer items, please", 422);
            }
        }

        if ($creator_items->total < $consumer_items->total) {
            return returnResult("your total amount is less than needed", 422);
        }

        $wpdb->insert($table_prefix . "lots", array(
            'creator_id' => $creator_id,
            'consumer_id' => $consumer_id,
            'status' => 'open',
            'creator_items' => json_encode($creator_items),
            'consumer_items' => json_encode($consumer_items),
        ));

        $last_id = $wpdb->insert_id;

        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_prefix . "lots WHERE id = " . $last_id . " ORDER BY id ")
        );

        return returnResult($result);
    }

    function get_available_lots(): array
    {
        global $wpdb, $table_prefix;
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "lots
        WHERE consumer_id = 0"
            )
        );

        return returnResult($result);
    }

    function get_own_lots(WP_REST_Request $request): array
    {
        $id_item = (int)$request['id'];
        global $wpdb, $table_prefix;

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "lots
        WHERE creator_id =" . $id_item
            )
        );

        return returnResult($result);
    }

    function get_user_lots(WP_REST_Request $request): array
    {
        $id_item = (int)$request['id'];
        global $wpdb, $table_prefix;

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "lots
        WHERE consumer_id =" . $id_item
            )
        );

        return returnResult($result);
    }

    function accept_lot(WP_REST_Request $request): array
    {
        global $wpdb, $table_prefix;
        $lot_id = (int)$request['lot_id'];
        $consumer_id = (int)$request['consumer_id'];

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $table_prefix . "lots
        WHERE id =" . $lot_id . " and consumer_id = 0 and status = 'open'"
            )
        );

        if (!$result) {
            return returnResult("no deals", 422);
        }

        $lot_items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_prefix . "lots WHERE id = " . $lot_id . " ORDER BY id ")
        );

        $consumer_items = json_decode($lot_items[0]->consumer_items);
        $creator_id = $lot_items[0]->creator_id;
        $creator_items = json_decode($lot_items[0]->creator_items);

        return extracted(
            $result,
            $consumer_id,
            $consumer_items,
            $creator_id,
            $creator_items,
            $wpdb,
            $table_prefix,
            $lot_id
        );
    }
}