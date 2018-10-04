<?php
/**
 * Minds DI (Dependency Injector)
 * @author Mark Harding (mark@minds.com)
 */
namespace Minds\Core\Di;

class Di
{
    private static $_;
    private $bindings = [];
    private $factories = [];

    /**
     * Return the binding for an alias
     * @param string $alias
     * @return mixed
     */
    public function get($alias)
    {
        print("alias --> " . $alias . "\n");
        print("function get --> 1\n");
        if (isset($this->bindings[$alias])) {
            print("function get --> 2\n");
            $binding = $this->bindings[$alias];
            print("function get --> 3\n");
            if ($binding->isFactory()) {
                print("function get --> 4\n");
                if (!isset($this->factories[$alias])) {
                    print("function get --> 5\n");
                    $this->factories[$alias] = call_user_func($binding->getFunction(), $this);
                    print("function get --> 6\n");
                }
                print("function get --> 7\n");
                return $this->factories[$alias];
            } else {
                print("function get --> 8\n");
                return call_user_func($binding->getFunction(), $this);
            }
        }
        return false;
    }

    /**
     * Bind an object to an alias
     * @param string $alias
     * @param Closure $function
     * @param array $options
     * @return void
     */
    public function bind($alias, \Closure $function, array $options = [])
    {
        $options = array_merge([
            'useFactory' => false,
            'immutable' => false
        ], $options);

        if ($options['immutable'] && isset($this->bindings[$alias])) {
            throw new ImmutableException();
        }

        $binding = (new Binding())
            ->setFunction($function)
            ->setFactory($options['useFactory'])
            ->setImmutable($options['immutable']);
        $this->bindings[$alias] = $binding;
    }

    /**
     * Singleton loader
     * @return Di
     */
    public static function _()
    {
        if (!self::$_) {
            self::$_ = new Di;
        }
        return self::$_;
    }
}
