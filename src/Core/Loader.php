<?php
/**
 * Registers all actions and filters for the plugin.
 *
 * This is the ONLY place in the entire plugin where add_action() and
 * add_filter() are called. Every other class registers its callbacks
 * here, and the Loader fires them all via run().
 *
 * This pattern centralises hook management, making the plugin easier
 * to debug, test, and extend without hunting through multiple files.
 *
 * @package TPots\EventBooking\Core
 */

namespace TPots\EventBooking\Core;

/**
 * Class Loader
 *
 * Maintains and registers all hooks for the plugin.
 */
class Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * Each element is an associative array with keys:
	 *   hook, component, callback, priority, accepted_args.
	 *
	 * @var array<int, array<string, mixed>> $actions
	 */
	private array $actions = array();

	/**
	 * The array of filters registered with WordPress.
	 *
	 * Same shape as $actions.
	 *
	 * @var array<int, array<string, mixed>> $filters
	 */
	private array $filters = array();

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param string   $hook          The name of the WordPress action.
	 * @param object   $component     A reference to the object that defines the callback.
	 * @param string   $callback      The name of the method on $component to call.
	 * @param int      $priority      Optional. Priority. Default 10.
	 * @param int      $accepted_args Optional. Number of args. Default 1.
	 */
	public function add_action(
		string $hook,
		object $component,
		string $callback,
		int $priority = 10,
		int $accepted_args = 1
	): void {
		$this->actions = $this->add(
			$this->actions,
			$hook,
			$component,
			$callback,
			$priority,
			$accepted_args
		);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param string   $hook          The name of the WordPress filter.
	 * @param object   $component     A reference to the object that defines the callback.
	 * @param string   $callback      The name of the method on $component to call.
	 * @param int      $priority      Optional. Priority. Default 10.
	 * @param int      $accepted_args Optional. Number of args. Default 1.
	 */
	public function add_filter(
		string $hook,
		object $component,
		string $callback,
		int $priority = 10,
		int $accepted_args = 1
	): void {
		$this->filters = $this->add(
			$this->filters,
			$hook,
			$component,
			$callback,
			$priority,
			$accepted_args
		);
	}

	/**
	 * A utility function used to register actions and filters into their
	 * respective collections.
	 *
	 * @param array<int, array<string, mixed>> $hooks         The existing hooks collection.
	 * @param string                           $hook          WordPress hook name.
	 * @param object                           $component     The object defining the callback.
	 * @param string                           $callback      The callback method name.
	 * @param int                              $priority      Hook priority.
	 * @param int                              $accepted_args Number of accepted arguments.
	 * @return array<int, array<string, mixed>> Updated hooks collection.
	 */
	private function add(
		array $hooks,
		string $hook,
		object $component,
		string $callback,
		int $priority,
		int $accepted_args
	): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register all collected actions and filters with WordPress.
	 *
	 * Called once from Plugin::run(). After this, WordPress knows
	 * about every hook the plugin needs.
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
