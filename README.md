# Kiaf---Kiaf Is Another Framework

## 要求--Requirement

本项目使用到：

* ?? NULL合并运算符
* 函数参数类型声明
* 函数返回值类型声明

因此，需要**PHP>=7.0**

## 使用--Usage

### 框架入口

```PHP
# 1、使用composer autoload
# 需要在composer.json中配置命名空间映射关系，如：
# {
#    "autoload": {
#        "psr-4": {
#            "app\\": "app/",
#            "kiaf\\": ["kiaf/", "kiaf/lib/"]
#        }
#    }
#}
namespace kiaf;

require('./vendor/autoload.php');
Kiaf::run('./app', true);

# 2、也可以选择使用框架自带的autoload
namespace kiaf;

require('./kiaf/Kiaf.php');
Kiaf::run('./app');
```

其中，run()函数是框架的运行入口，其接受两个参数：

```PHP
Kiaf::run(string app_path, boolean use_composer_autoload)
string app_path: 应用根目录
boolean use_composer_autoload: 是否使用composer的自动加载
```

**每次修改composer.json后都需要使用```composer update```来更新**

### 配置文件

```json
# 位置：app/config/config.php
# 命名空间-路径 映射关系
'namespace_map' => [

],

# 应用下根命名空间
'app_root_namespace' => 'app\\',

# 默认模块、控制器、方法
'default_module' => 'home',
'default_controller' => 'index',
'default_action' => 'index',

# 视图左右定界符
'left_delimiter' => '[{',
'right_delimiter' => '}]',

# 跳转模板
'jump_tpl' => 'default_jump.tpl',

# 数据库配置
'db_type' => 'mysql',
'db_host' => '127.0.0.1',
'db_port' => '3306',
'db_user' => 'root',
'db_pwd' => '',
'db_selected_db' => '',
'db_char_set' => 'utf8mb4',
```


## 编码规范

本项目编码风格遵循PSR-2，自动加载遵循PSR-4

另外：

* 属性命名采用**下划线分隔式**，如$user_name
* 文件末尾保留一行空白行作为文件结束（EOF）

## 附

### PSR-1

* PHP代码文件*必须*以```<?php``` 或 ```<?=```标签开始

* PHP代码文件*必须*以*不带```BOM```*的```UTF-8```编码

* PHP代码中*应该*只定义类、函数、常量等声明，或其他会产生副作用的操作（如：生成文件输出以及修改 .ini 配置文件等），二者只能选其一

* 命名空间以及类*必须*符合 PSR 的自动加载规范：[PSR-4](#user-content-PSR-4) 中的一个

* 类的命名*必须*遵循```StudlyCaps```大写开头的驼峰命名规范

* 类中的常量所有字母都*必须*大写，单词间用下划线分隔

* 方法名称*必须*符合```camelCase``` 式的小写开头驼峰命名规范

### PSR-2

* 代码*必须*遵循 [PSR-1](#user-content-PSR-1) 中的编码规范

* 代码*必须*使用4个空格符而不是「Tab 键」进行缩进

* 每行的字符数*应该*软性保持在 80 个之内，理论上*一定不可*多于 120 个，但*一定不可*有硬性限制。

* 每个```namespace```命名空间声明语句和```use```声明语句块后面，*必须*插入一个空白行

* 类的开始花括号（{）*必须*写在函数声明后自成一行，结束花括号（}）也*必须*写在函数主体后自成一行

* 方法的开始花括号（{）*必须*写在函数声明后自成一行，结束花括号（}）也*必须*写在函数主体后自成一行

* 类的属性和方法*必须*添加访问修饰符（private、protected 以及 public），abstract 以及 final *必须*声明在访问修饰符之前，而 static *必须*声明在访问修饰符之后

* 控制结构的关键字后*必须*要有一个空格符，而调用方法或函数时则*一定不可*有

* 控制结构的开始花括号（{）*必须*写在声明的同一行，而结束花括号（}）*必须*写在主体后自成一行

* 控制结构的开始左括号后和结束右括号前，都*一定不可*有空格符


### PSR-4

1. 以下的「类」泛指所有的「Class类」、「接口」、「traits 可复用代码块」以及其它类似结构
2. 一个完整的类名需具有以下结构:

  ```php
  \<命名空间>(\<子命名空间>)*\<类名>
  ```

  * 完整的类名*必须*要有一个顶级命名空间，被称为 "vendor namespace"
  * 完整的类名*可以*有一个或多个子命名空间
  * 完整的类名*必须*有一个最终的类名
  * 完整的类名中任意一部分中的下滑线都是没有特殊含义的
  * 完整的类名*可以*由任意大小写字母组成
  * 所有类名都*必须*是大小写敏感的

3. 当根据完整的类名载入相应的文件
  * 完整的类名中，去掉最前面的命名空间分隔符，前面连续的一个或多个命名空间和子命名空间，作为「命名空间前缀」，其*必须*与至少一个「文件基目录」相对应
  * 紧接命名空间前缀后的子命名空间*必须*与相应的「文件基目录」相匹配，其中的命名空间分隔符将作为目录分隔符
  * 末尾的类名*必须*与对应的以```.php```为后缀的文件同名
  * 自动加载器（autoloader）的实现*一定不可*抛出异常、*一定不可*触发任一级别的错误信息以及*不应该*有返回值
