# Report generator

Used to create reports in pdf 

### scripts 

To run tests just run
```bash
composer test 
```

To run tests inside docker 

```bash
composer docker-test
```

or if you dont have installed composer 
```bash 
bash bin/.docker-test
```

Linter

```bash
composer lint "fix --dry-run"
```

Linter fix

```bash
composer lint "fix"
```
