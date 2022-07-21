<?php

namespace RetailCrm\ServiceBundle\Messenger;

abstract class CommandMessage
{
    protected string $commandName;

    protected array $options = [];

    protected array $arguments = [];

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function setCommandName(string $commandName): void
    {
        $this->commandName = $commandName;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function addOption(string $key, string $value): void
    {
        $this->options[$key] = $value;
    }

    public function addArgument(string $key, string $value): void
    {
        $this->arguments[$key] = $value;
    }

    public function getFormattedOptions(): array
    {
        $options = [];
        foreach ($this->options as $option => $value) {
            $options['--' . $option] = $value;
        }

        return $options;
    }

    public function __serialize(): array
    {
        return [
            'commandName' => $this->getCommandName(),
            'arguments' => $this->getArguments(),
            'options' => $this->getOptions()
        ];
    }
}
