<?php
namespace Mcpuishor\LinodeLaravel;

class LinodeClient
{
    protected Transport $transport;

    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(app(Transport::class)))
            ->$name(...$arguments);
    }

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    static public function make():self
    {
        return new static(
            app(Transport::class)
        );
    }

    protected function instances()
    {
        return new Instances($this->transport);
    }

    protected function databases()
    {
        return new Database($this->transport);
    }

    protected function regions()
    {
        return new Regions($this->transport);
    }
}
