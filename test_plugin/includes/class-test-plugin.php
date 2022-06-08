<?php

class Test_Plugin
{

    public const REST_API_INIT = 'rest_api_init';
    public const GET_METHOD = 'GET';
    public const POST_METHOD = 'POST';
    public const DELETE_METHOD = 'DELETE';

    public function init()
    {
        $this->load_dependencies();
        $this->define_api_routes();
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api-route-functions.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/test-functions.php';
    }

    private function define_api_routes()
    {
        $namespace = 'test';
        $id_pattern = '(?P<id>\d+)';

        function set_route_params(string $method, $callback, array $args = [])
        {
            $route_params = [
                'methods' => $method,
                'callback' => $callback
            ];
            if ($args) {
                $route_params['args'] = $args;
            }

            return $route_params;
        }

        add_action(self::REST_API_INIT, function () use ($namespace, $id_pattern) {
            //User api
            register_rest_route($namespace, '/get-users/', set_route_params(self::GET_METHOD, [UserResource::class, 'get_users']));
            register_rest_route($namespace, '/get-user/' . $id_pattern, set_route_params(self::GET_METHOD, [UserResource::class, 'get_user'], ['id']));
            register_rest_route($namespace, '/edit-user/' . $id_pattern, set_route_params(self::POST_METHOD, [UserResource::class, 'edit_user'], ['id']));
            register_rest_route($namespace, '/add-user/', set_route_params(self::POST_METHOD, [UserResource::class, 'add_user']));
            register_rest_route($namespace, '/delete-user/' . $id_pattern, set_route_params(self::DELETE_METHOD, [UserResource::class, 'delete_user'], ['id']));
            //items api
            register_rest_route($namespace, '/get-items/', set_route_params(self::GET_METHOD, [ItemResource::class, 'get_items']));
            register_rest_route($namespace, '/get-item/' . $id_pattern, set_route_params(self::GET_METHOD, [ItemResource::class, 'get_item'], ['id']));
            register_rest_route($namespace, '/add-item/', set_route_params(self::POST_METHOD, [ItemResource::class, 'add_item']));
            register_rest_route($namespace, '/delete-item/' . $id_pattern, set_route_params(self::DELETE_METHOD, [ItemResource::class, 'delete_item'], ['id']));
            //bid api
            register_rest_route($namespace, '/add-lot/', set_route_params(self::POST_METHOD, [BidResource::class, 'add_lot']));
            register_rest_route($namespace, '/get-available-lots/', set_route_params(self::GET_METHOD, [BidResource::class, 'get_available_list']));
            register_rest_route($namespace, '/get-own-lots/' . $id_pattern, set_route_params(self::GET_METHOD, [BidResource::class, 'get_own_lots'], ['id']));
            register_rest_route($namespace, '/get-user-lots/' . $id_pattern, set_route_params(self::GET_METHOD, [BidResource::class, 'get_user_lots'], ['id']));
            register_rest_route($namespace, '/accept-lot/', set_route_params(self::POST_METHOD, [BidResource::class, 'accept_lot']));
        });
    }

}