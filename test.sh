set -e

php bin/console doctrine:schema:drop -f
php bin/console doctrine:schema:update -f

symfony server:start -d
printf "\n\n\n[Test.sh] Sending new user requests\n\n"
curl -d '{"email":"a@a.a", "username":"aa"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
printf "\n\n"
curl -d '{"email":"a@a.a", "username":"aaaa"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
printf "\n\n"
curl -d '{"email":"b@b.b", "username":"b"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
printf "\n\n\n"
symfony server:stop