## EXAMPLES

`parse` command requires one argument: `url`
```
./bin/app parse sport.ua
```

`report` command requires one argument: `url` 
```
./bin/app report sport.ua
```

`help` command does not require arguments
```
./bin/app help
```

### NOTE
```
Parse command check 50 unique nested links by default. 
U can change this value in ./src/Console/Command/ParseCommand.php
```

