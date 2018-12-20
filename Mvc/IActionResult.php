<?php

namespace Mvc;

interface IActionResult {
    public function execute() : string;
}