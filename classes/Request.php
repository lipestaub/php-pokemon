<?php
    class Request {
        private string $method;
        private string $route;

        public function __construct()
        {
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->route = $_SERVER['REQUEST_URI'];
        }

        public function testRequest() {
            if ($this->method !== 'GET') {
                http_response_code(400);

                echo json_encode(['message' => 'Invalid method provided.']);
                exit;
            }
        }

        public function getMethod() {
            return $this->method;
        }

        public function setMethod(string $method) {
            $this->method = $method;
        }

        public function getRoute() {
            return $this->route;
        }

        public function setRoute(string $route) {
            $this->route = $route;
        }
    }
?>