Configure services for this bundle in your project as in example:

```yaml
services:
  GepurIt\RemoteProcedureCallBundle\Rabbit\ExchangeProviderInterface:
    alias: 'rpc.provider.your_service_1'

  GepurIt\RemoteProcedureCallBundle\RpcClient\RpcClientInterface:
    alias: 'rpc.your_service_1'

# 1 -- default
  rpc.provider.your_service_1:
    class: GepurIt\RemoteProcedureCallBundle\Rabbit\ExchangeProvider
    arguments: ['@rabbit_mq_service_1', 'your_queue_1']
    public: true

  rpc.your_service_1:
    class: GepurIt\RemoteProcedureCallBundle\RpcClient\RemoteProcedureCallClient
    autowire: true
    arguments:
      $exchangeProvider: 'rpc.provider.your_service_1'
    public: true

# 2
  rpc.provider.your_service_2:
    class: GepurIt\RemoteProcedureCallBundle\Rabbit\ExchangeProvider
    arguments: ['@rabbit_mq_service_2', 'your_queue_2']
    public: true

  rpc.your_service_2:
    class: GepurIt\RemoteProcedureCallBundle\RpcClient\RemoteProcedureCallClient
    autowire: true
    arguments:
      $exchangeProvider: 'rpc.provider.your_service_2'
    public: true
```