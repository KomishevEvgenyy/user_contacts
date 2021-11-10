# user balance by all number
SELECT users.name, SUM(phones.balance) as sum FROM operators.phones as phones
INNER JOIN operators.users as users ON phones.user_id=users.id GROUP BY users.id;

# user balance by operator
SELECT users.name, phones.code, SUM(phones.balance) as sum FROM operators.phones as phones
INNER JOIN operators.users as users ON phones.user_id=users.id GROUP BY users.id, phones.code;

# number of numbers at the operator
SELECT phones.code, COUNT(phones.number) FROM operators.phones group by phones.code;

# count phone in the user
SELECT users.name AS name, COUNT(phones.id) AS sum FROM operators.users AS users
JOIN operators.phones AS phones ON users.id=phones.user_id
group by users.id;

# get 10 users who have max balance
SELECT users.name, phones.balance as balance FROM operators.users AS users
JOIN operators.phones as phones ON users.id=phones.user_id ORDER BY balance DESC LIMIT 10;

