# PHP ICS Parser

--

[![Latest Stable Version](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png)](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png)](https://packagist.org/packages/johngrogg/ics-parser)

## License

This ics-parser is under [MIT License](http://opensource.org/licenses/MIT). You may use it for your own sites for free, but I would like to get a notice when you use it (info@martin-thoma.de). If you use it for another software project, please state the information / links to this project in the files.

It is hosted at [https://github.com/MartinThoma/ics-parser/](https://github.com/MartinThoma/ics-parser/) and the PEAR coding standard is used.

It was modified by John Grogg to properly handle recurring events (specifically with regards to Microsoft Exchange).

## Requirements
  - PHP 5 >= 5.3.0

## Installation

### Composer

[Composer](http://getcomposer.org)

```bash
$ curl -s https://getcomposer.org/installer | php
```

`composer.json`

```yaml
{
    "require": {
        "johngrogg/ics-parser": "dev-master"
    }
}
```

## Credits
  - Martin Thoma (programming, bug fixing, project management)
  - Frank Gregor (programming, feedback, testing)
  - John Grogg (programming, addition of event recurrence handling)
  - [Jonathan Goode](https://github.com/u01jmg3) (programming, bug fixing, enhancement, coding standard)