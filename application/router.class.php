<?php

class router {
    /*
     * @the registry
     */

    private $registry;

    /*
     * @the controller path
     */
    private $path;
    private $args = array();
    public $file;
    public $controller;
    public $action;

    function __construct($registry) {
        $this->registry = $registry;
    }

    /**
     *
     * @set controller directory path
     *
     * @param string $path
     *
     * @return void
     *
     */
    function setPath($path) {

        /*         * * check if path i sa directory ** */
        if (is_dir($path) == false) {
            throw new Exception('Invalid controller path: `' . $path . '`');
        }
        /*         * * set the path ** */
        $this->path = $path;
    }

    /**
     *
     * @load the controller
     *
     * @access public
     *
     * @return void
     *
     */
    public function loader() {
        /*         * * check the route ** */
        $parts = $this->getController();

        /*         * * if the file is not there assume index ** */
        if (is_readable($this->file) == false) {
            $this->file = $this->path . '/indexController.php';
            $this->controller = 'index';
        } else {
            array_shift($parts); //remove controller to allow uri variable capture
        }

        /*         * * include the controller ** */
        include $this->file;

        /*         * * a new controller class instance ** */
        $class = $this->controller . 'Controller';
        $controller = new $class($this->registry);

        $num_uri_parts = count($parts);

        //if odd amount of uri variables then make the first the id field
        if ($num_uri_parts % 2 !== 0) {
            $gets["id"] = $parts[0];
            array_shift($parts);
        }

        //set array into associotive array with according values from uri
        for ($x = 0; $x < count($parts); $x = $x + 2) {
            $gets[$parts[$x]] = $parts[$x + 1];
        }
        $this->registry->args = !empty($gets) ? $gets : null;


        /*         * * check if the action is callable ** */
        if (is_callable(array($controller, $this->action)) == false) {
            $action = 'index';
        } else {
            $action = $this->action;
        }
        /*         * * run the action ** */
        $controller->$action();
    }

    /**
     *
     * @get the controller
     *
     * @access private
     *
     * @return void
     *
     */
    private function getController() {

        $parts = array();

        /*         * * get the route from the url ** */
        $route = (empty($_GET['rt'])) ? '' : $_GET['rt'];

        if (empty($route)) {
            $route = 'index';
        } else {
            /*             * * get the parts of the route ** */
            $parts = explode('/', $route);
            $checkFile = $this->path . '/' . $parts[0] . 'Controller.php';

            //if a control file exists make it so, else default to index
            //make the action appropriate too
            if (file_exists($checkFile)) {
                $this->controller = $parts[0];
                !isset($parts[1]) ? : $this->action = $parts[1];
            } else {
                $this->controller = 'index';
                !isset($parts[0]) ? : $this->action = $parts[0];
            }
        }

        if (empty($this->controller)) {
            $this->controller = 'index';
        }

        /*         * * Get action ** */
        if (empty($this->action)) {
            $this->action = 'index';
        }

        /*         * * set the file path ** */
        $this->file = $this->path . '/' . $this->controller . 'Controller.php';

        /*         * * return array to allow transfer of url variable ** */
        return $parts;
    }

}
