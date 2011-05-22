<?php

class ShellComponent extends Component {

	/**
	 * Controller instance
	 *
	 * @var Controller
	 */
	private $__controller = null;
	/**
	 * Component settings
	 *
	 * @var array
	 */
	private $__settings = array();

	/**
	 * Initialize component's settings
	 *
	 * @param object $controller Controller instance
	 * @param array $settings Component setings
	 * @return void
	 */
	public function initialize(&$controller, $settings = array()) {
		$this->__controller = $controller;
		$this->__settings = array_merge(array(
			'consolePath' => ROOT . DS . 'cake' . DS . 'console' . DS . 'cake.php',
			'phpPath' =>  '/usr/bin/php',
		), (array)$settings);
	}

	/**
	 * Run shell task
	 *
	 * Example:
	 * $this->Shell->run('shell_name', 'task_name', array('param1' => 1, 'param2' => 2), false);
	 *
	 * or array-like:
	 *
	 * $this->Shell->run(array(
	 *   'shell' => 'shell_name',
	 *   'task' => 'task_name',
	 *   'params' => array('param1' => 1, 'param2' => 2),
	 *   'background' => false
	 * );
	 *
	 * @param string $shell Shell name
	 * @param string $task Task to run
	 * @param array $params Parameters to pass to shell task
	 * @param array $background Whether to run the task in background
	 * @return mixed String as shell task output, boolean false when shell/task not found
	 */
	public function run($shell, $task = null, $params = array(), $background = false) {
		if (is_array($shell)) {
			extract($shell);
		}

		if (!App::import('Shell', $shell)) {
			return false;
		}

		$cmd = array($shell);
		$append = '';
		$prepend = '';

		if (isset($task) && !empty($task)) {
			$cmd[] = escapeshellcmd($task);
		}

		if (isset($params) && !empty($params)) {
			foreach ((array)$params as $key => $val) {
				$cmd[] = '-' . $key . ' ' . escapeshellarg($val);
			}
		}

		if (isset($background) && $background) {
			$prepend = 'nohup ';
			$append = ' > /dev/null &';
		}

		$cmd = $prepend . $this->__settings['phpPath'] . ' ' . $this->__settings['consolePath'] . ' ' . implode(' ', $cmd) . $append;
		return exec($cmd);
	}

}
