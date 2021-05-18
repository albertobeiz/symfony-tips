set -e

php bin/console doctrine:schema:drop -f -q
php bin/console doctrine:schema:update -f -q

symfony server:start -d -q
printf "\n\n\n[Test.sh] Sending new user requests\n\n"
curl -d '{"email":"a@a.a", "username":"aa"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
printf "\n\n"
curl -d '{"email":"a@a.a", "username":"aaaa"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
printf "\n\n"
curl -d '{"email":"b@b.b", "username":"b"}' -H "Content-Type: application/json" -X POST https://localhost:8000/users
symfony server:stop -q

printf "\n\n\n[Test.sh] Waiting before processing Async events...\n\n"
sleep 5
php bin/console messenger:consume async -q --time-limit=1
