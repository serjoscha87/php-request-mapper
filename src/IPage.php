<?php

/**
 * @noinspection PhpUnused
 */

namespace serjoscha87\phpRequestMapper;

interface IPage {

    public function __toString() : string;
    public function getName() : string|null;

    public function getFilePath () : string|null;
    public function getBasePath () : string|null;

    public function is404Page () : bool;
    public function isDetailPage () : bool;
    public function isDefaultPage () : bool;

    public function getRequestMapper () : RequestMapper|null;

}
