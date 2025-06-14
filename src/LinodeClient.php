<?php
namespace Mcpuishor\LinodeLaravel;

class LinodeClient
{
    protected Transport $transport;

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

    public function instances()
    {
        return new Instances($this->transport);
    }

    public function databases()
    {
        return new Database($this->transport);
    }

    public function regions()
    {
        return new Regions($this->transport);
    }
}
