# API

Generate the SSH keys (for the lexik-jwt authentication):

`
$ mkdir config/jwt
`

`
$ openssl genrsa -out var/jwt/private.pem -aes256 4096
`

`
$ openssl rsa -pubout -in var/jwt/private.pem -out config/jwt/public.pem
`