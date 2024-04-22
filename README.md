# phpcs/phpcbf plugin for phpcq.

This plugin provides [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) integration for phpcq.

It also provides a "fix" option, which runs [phpcbf](https://github.com/squizlabs/PHP_CodeSniffer) on the code base.

## Configuration


Extend your `.phpcq.yaml.dist` configuration by adding the plugin and configuring the task:

```yaml
phpcq:
  plugins:
    phpcs:
      version: ^1.0
      signed: false

tasks:
  phpcs:
    config: &phpcs-config
      # By default, PSR12 is used
      standard: ~
      standard_paths:
        - 'vendor/path/to/coding-standard'
      excluded:
        - 'some/excluded_path'
      excluded_sniffs:
        - 'Excludedniff'
      # Define paths which should be loaded when PHPCS is bootstrapping.
      # Useful for enhancement plugins which provides a coding standard
      autoload_paths:
        - 'vendor/autoload.php'

  # The phpcbf tool is activated when activating the fix flag
  phpcbf:
    plugin: phpcs
    config:
      <<: *phpcs-config
      fix: true
```
