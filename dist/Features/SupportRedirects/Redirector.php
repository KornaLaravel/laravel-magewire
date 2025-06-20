<?php
/**
 * Livewire copyright © Caleb Porzio (https://github.com/livewire/livewire).
 * Magewire copyright © Willem Poortman 2024-present.
 * All rights reserved.
 *
 * Please read the README and LICENSE files for more
 * details on copyrights and license information.
 */
namespace Magewirephp\Magewire\Features\SupportRedirects;

use Magewirephp\Magewire\Component;
use Illuminate\Routing\Redirector as BaseRedirector;
class Redirector extends BaseRedirector
{
    protected $component;
    public function to($path, $status = 302, $headers = [], $secure = null)
    {
        $this->component->redirect($this->generator->to($path, [], $secure));
        return $this;
    }
    public function away($path, $status = 302, $headers = [])
    {
        return $this->to($path, $status, $headers);
    }
    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v) {
            $this->session->flash($k, $v);
        }
        return $this;
    }
    public function component(Component $component)
    {
        $this->component = $component;
        return $this;
    }
    public function response($to)
    {
        return $this->createRedirect($to, 302, []);
    }
}