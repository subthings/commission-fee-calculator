## Commission fee calculator app

# Requirements
PHP = 7.3.*

# Installation
- `./install`
- add CURRENCY_CONVERTER_ACCESS_KEY in .env (that is API Access Key from https://currencylayer.com, to get it create account, for the app free account will be enought)

# To run file import
`./run php calculator commission:calculate input.csv`

Pay attention that input.csv should have following structure (and any name):

1) operation date in format Y-m-d
2) user's identificator, number
3) user's type, one of private or business
4) operation type, one of deposit or withdraw
5) operation amount (for example 2.12 or 3)
6) operation currency, one of EUR, USD, JPY

# To run tests
`./run composer phpunit`
