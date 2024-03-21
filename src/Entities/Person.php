<?php

namespace NsTest\Entities;

abstract class Person
{
    public int $id;
    public string $name;
    public string $email;
    public string $mobile;

    abstract public static function getById(int $id): static;

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}