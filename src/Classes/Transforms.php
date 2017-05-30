<?php

namespace Hoppermagic\Kobalt\Classes;

abstract class Transforms
{
    const NONE          = 0x01;
    const RESIZE        = 0x02;
    const CROP          = 0x03;
//    const HEIGHT  = 0x03;
//    const AUTO    = 0x04;
    const FIT           = 0x05;
    const FIT_TO_BOX    = 0x05;
//    const PRECISE = 0x06;
}