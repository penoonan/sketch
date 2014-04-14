<?php

namespace Sketch\Metabox;

use Sketch\Dispatcher;
use Sketch\WpApiWrapper;

class SketchMetaboxMissingControllerException extends \Exception {}
class SketchMetaboxInvalidControllerException extends \InvalidArgumentException {}

class BaseMetabox implements MetaboxInterface {

    protected
      $id = 'metabox-id',
      $title = 'Metabox',
      $post_type = 'post',
      $screen = null,
      $context = 'advanced',
      $priority = 'default',
      $callback_args = array(),
      $callback_controller = false
    ;

    /**
     * @var \Sketch\Dispatcher
     */
    private $dispatcher;
    /**
     * @var \Sketch\WpApiWrapper
     */
    private $wp;

    /**
     * @param WpApiWrapper $wp
     * @param Dispatcher $dispatcher
     */
    public function __construct(WpApiWrapper $wp, Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->wp = $wp;
    }

    public function add()
    {
        $this->validate();
        $this->wp->add_meta_box(
          $this->id,
          $this->title,
          array($this, 'dispatch'),
          $this->screen ?: $this->post_type,
          $this->context,
          $this->priority,
          $this->callback_args
        );
    }

    public function manuallyAddAction()
    {
        $this->wp->add_action('add_meta_boxes', array($this, 'add'));
    }

    public function dispatch($post, $meta_box)
    {
        $dispatch_args = array($post, $meta_box);
        if (count($this->callback_args) > 0) {
            $dispatch_args[] = $this->callback_args;
        }

        $this->dispatcher->dispatch($this->callback_controller, $dispatch_args);
    }

    protected function callbackArgs()
    {
        return count($this->callback_args > 0) ? $this->callback_args : null;
    }

    private function validate()
    {
        if (!$this->callback_controller) {
            Throw new SketchMetaboxMissingControllerException('Metabox ' . get_class($this) . ' has no callback controller defined');
        }

        if (FALSE == strpos($this->callback_controller, '@')) {
            Throw new SketchMetaboxInvalidControllerException('Controller "' . $this->callback_controller . '" specified in class ' . get_class($this) . ' is incorrectly formatted. E.g., the correct format is "home@index", which would run the index() method on the class HomeController.');
        }
    }


} 