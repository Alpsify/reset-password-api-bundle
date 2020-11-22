<?php

namespace Alpsify\ResetPasswordAPIBundle;

use Alpsify\ResetPasswordAPIBundle\DependencyInjection\AlpsifyResetPasswordExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlpsifyResetPasswordAPIBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new AlpsifyResetPasswordExtension();
        }
        return $this->extension;
    }
}
