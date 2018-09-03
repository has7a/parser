## EXAMPLES

`parse` command requires one argument: `url`
```
./bin/console parse sport.ua
```

`report` command requires one argument: `url` 
```
./bin/console report sport.ua
```

`help` command does not require arguments
```
./bin/console help
```

### NOTE
```
Parse command check 50 unique nested links by default. 
U can change this value in ./src/Console/Command/ParseCommand.php
```

