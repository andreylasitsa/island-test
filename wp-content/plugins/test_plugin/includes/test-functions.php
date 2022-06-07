<?php

function generateItems($user_id)
{
    global $wpdb, $table_prefix;
    $total_value = rand(3, 20);

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "items"));

    $filter_items = filterItems($total_value, $result);

    while ($total_value != 0) {
        $rand_item = rand(0, count($filter_items) - 1);
        $rand_item = $filter_items[$rand_item];

        $wpdb->insert($table_prefix . "user_items", array(
            'id_user' => $user_id,
            'id_item' => $rand_item->id,
        ));

        $total_value = $total_value - $rand_item->value;
        $filter_items = filterItems($total_value, $filter_items);
    }

}

function filterItems($total_value, $items)
{
    $filter_items = [];
    foreach ($items as $item) {
        if ($item->value <= $total_value) {
            $filter_items[] = $item;
        }
    }
    return $filter_items;
}

function checkUser($user_id)
{
    global $wpdb, $table_prefix;

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "test_users WHERE id = " . $user_id . " ORDER BY id "));
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function checkUserItems($user_id, $items)
{
    global $wpdb, $table_prefix;

    $items_DB = $wpdb->get_results($wpdb->prepare("SELECT " . $table_prefix . "items.name FROM 
   `" . $table_prefix . "test_users` INNER JOIN " . $table_prefix . "user_items ON " . $table_prefix . "user_items.id_user = 
   " . $table_prefix . "test_users.id INNER JOIN " . $table_prefix . "items ON " . $table_prefix . "items.id = 
   " . $table_prefix . "user_items.id_item WHERE " . $table_prefix . "test_users.id = " . $user_id));

    $items_DB = createArrayItems($items_DB);
    $diff_array = array_diff($items->items, $items_DB);

    if (!empty($diff_array)) {
        return false;
    } else {
        if (checkTotal($items)) {
            return true;
        } else {
            return false;
        }
    }
}

function createArrayItems($array_object_items)
{
    $array = [];
    foreach ($array_object_items as $item) {
        $array[] = $item->name;
    }
    return $array;
}

function checkTotal($items)
{
    global $wpdb, $table_prefix;

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "items"));

    if ($items->total) {
        $total_count = 0;
        foreach ($items->items as $item) {
            foreach ($result as $res) {
                if ($item === $res->name) {
                    $total_count = $total_count + $res->value;
                }
            }
        }
        if ($items->total === $total_count) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function returnResult($result, $code = 200, $items = null): array
{
    if (!$items) {
        return array(
            'code' => $code,
            'result' => $result
        );
    }
    return array(
        'code' => $code,
        'result' => $result,
        'items' => $items
    );

}