<?php

namespace Synaptic4UParser\Core;

use Exception;
use Synaptic4UParser\UI\IUserInterface;

class CallUserFunction
{
    public function callUserFunc(IUserInterface $ui, array $params = [], string $method = null): mixed
    {
        $reply = [];

        try {
            if (method_exists($ui, $method)) {
                $this->log([
                    'Location' => __METHOD__.'(): '.$method.' exists',
                    'params' => json_encode($params, JSON_PRETTY_PRINT),
                ]);

                $reply = call_user_func_array(
                    [
                        $ui,
                        $method,
                    ],
                    [
                        &$params,
                    ]
                );

                // $reply = $ui->{$method}($params);

                if (null === $reply) {
                    throw new Exception('Controller: '.get_class($ui).' Method: '.$method.'. : RETURNED NULL!!!');
                }

                $this->log([
                    'Location' => __METHOD__.'()',
                    'reply' => json_encode($reply, JSON_PRETTY_PRINT),
                    'params' => json_encode($params, JSON_PRETTY_PRINT),
                ]);
            } else {
                throw new Exception("Can't find this method!");
            }
        } catch (Exception $e) {
        } finally {
            return $reply;
        }
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, 'activity');
    }
}
