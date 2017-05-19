<?php declare(strict_types=1);

namespace NetAccessory;

class View {
    
    protected $options = [
        'main' => 'index',
        'layout' => 'index',
        'dir' => 'views'
    ];
    
    public function __construct(string $file, array $vars = [], array $options = []) {
        $this->file = $file;
        $this->vars = $vars;
        $this->options = array_merge($this->options, $options);
    }
    
    public function render(string $partial = null) {
        if(!$partial)
            return (string) $this;
            
        $view = new View($partial, $this->vars, $this->options);
        return (string) $view;
    }
    
    public function __toString() {
        ob_start();
        extract($this->vars);
        include(realpath(__DIR__ . '/' . $this->options['dir'])."/{$this->file}.php");
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
}