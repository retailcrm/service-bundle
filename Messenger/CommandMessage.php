<?php

namespace RetailCrm\ServiceBundle\Messenger;

/**
 * Class Message
 *
 * @package RetailCrm\ServiceBundle\Messenger
 */
abstract class CommandMessage
{
    /** @var string */
    protected $commandName;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $arguments = [];

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * @param string $commandName
     */
    public function setCommandName(string $commandName): void
    {
        $this->commandName = $commandName;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addOption(string $key, string $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addArgument(string $key, string $value): void
    {
        $this->arguments[$key] = $value;
    }

    /**
     * @return array
     */
    public function getFormattedOptions(): array
    {
        $options = [];
        foreach ($this->options as $option => $value) {
            $options['--' . $option] = $value;
        }

        return $options;
    }

    /**
     * For lockable message
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'command' => $this->getCommandName(),
            'arguments' => $this->getArguments(),
            'options' => $this->getOptions()
        ];
    }
}
