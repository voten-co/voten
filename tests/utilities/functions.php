<?php

function create($class, $attributes = [])
{
    return factory($class)->create($attributes);
}

function createFromState($class, $state, $attributes = [])
{
    return factory($class)->states($state)->create($attributes);
}

function make($class, $attributes = [])
{
    return factory($class)->make($attributes);
}

function makefromState($class, $state, $attributes = [])
{
    return factory($class)->states($state)->make($attributes);
}
