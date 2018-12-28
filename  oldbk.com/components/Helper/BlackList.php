<?php

namespace components\Helper;


class BlackList
{
    const ALLOW = true;
    const DENY = false;

    protected $settings;
    protected $app;

    public function __construct($app, array $settings = [])
    {
        $defaults = [
            'callback' => function () use ($app) {
                $app->halt(403, 'Ваш IP временно заблокирован!');
            },
            'list' => [],
        ];
        $this->app = $app;
        $this->settings = $settings + $defaults;
    }

    public function allow($cidr)
    {
        if (is_array($cidr)) {
            foreach ($cidr as $item) {
                $this->settings['list'][$item] = self::ALLOW;
            }
        } else {
            $this->settings['list'][$cidr] = self::ALLOW;
        }

        return $this;
    }

    public function deny($cidr)
    {
        if (is_array($cidr)) {
            foreach ($cidr as $item) {
                $this->settings['list'][$item] = self::DENY;
            }
        } else {
            $this->settings['list'][$cidr] = self::DENY;
        }

        return $this;
    }

    public function __invoke()
    {
        foreach ($this->settings['list'] as $cidr => $allow) {
            $cidr = strtolower(trim($cidr));
            if ($cidr == 'all' or self::cidrMatch(
                    $cidr,
                    $this->app->environment['REMOTE_ADDR']
                )) {
                if (!$allow)
                    break;
                return;
            }
        }
        if (is_callable($this->settings['callback'])) {
            $this->settings['callback']();
            return;
        }
        $this->app->response->setStatus(403);
    }

    public static function cidrMatch($cidr, $address)
    {
        list($subnet, $slash, $size) = preg_split(
            '@(/|$)@',
            $cidr,
            2,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $size = 32 - intval($slash ? $size : 32);
            return ip2long($subnet) == (ip2long($address) & (-1 << $size));
        } elseif (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $size = intval($slash ? $size : 128);
            return inet_pton($subnet) == (inet_pton($address) & pack(
                        'H*',
                        str_pad(
                            str_repeat('f', $size / 4) . ['', '8', 'c', 'e'][$size % 4],
                            32,
                            '0'
                        )
                    ));
        }
        return false;
    }
}