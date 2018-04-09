<?php

namespace Illuminate\Database;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use InvalidArgumentException;

abstract class Seeder
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * Seed the given connection from the given path.
     *
     * @param  string $class
     * @return void
     */
    public function call($class)
    {
        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeding:</info> $class");
        }

        $this->resolve($class)->__invoke();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke()
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidArgumentException('Method [run] missing from ' . get_class($this));
        }

        return isset($this->container)
            ? $this->container->call([$this, 'run'])
            : $this->run();
    }

    /**
     * Resolve an instance of the given seeder class.
     *
     * @param  string $class
     * @return \Illuminate\Database\Seeder
     */
    protected function resolve($class)
    {
        if (isset($this->container)) {
            $instance = $this->container->make($class);

            $instance->setContainer($this->container);
        } else {
            $instance = new $class;
        }

        if (isset($this->command)) {
            $instance->setCommand($this->command);
        }

        return $instance;
    }

    /**
     * Silently seed the given connection from the given path.
     *
     * @param  string $class
     * @return void
     */
    public function callSilent($class)
    {
        $this->resolve($class)->__invoke();
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Set the console command instance.
     *
     * @param  \Illuminate\Console\Command $command
     * @return $this
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }
}
