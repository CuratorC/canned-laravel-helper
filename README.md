<h1 align="center"> canned-laravel-helper </h1>

<p align="center"> laravel 辅助函数.</p>


## Installing

### Git 库引用（推荐）

```shell
composer require curatorc/canned-laravel-helper
```

### 本地引用

* 克隆仓库至项目目录的平级目录
  > 在项目根目录的上一级目录运行
  ```shell
  git clone git@github.com:CuratorC/canned-laravel-helper.git
  ```

* 添加本地库源
  > 在项目根目录运行
  ```shell
  composer config repositories.canned-laravel-helper path ../canned-laravel-helper
  ```

* 引用`SDK`
  ```shell
  composer require curatorc/canned-laravel-helper:dev-master
  ```

## Usage

### Publish Basic Class

```shell
php artisan vendor:publish --provider="CuratorC\CannedLaravelHelper\ServiceProvider"
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/curatorc/canned-laravel-helper/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/curatorc/canned-laravel-helper/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT