## Symfony Tips #14 - Separate your application in modules

What if your app keeps growing and growing? Then it can be a good idea to split it into modules.

### This is OK

![Captura de pantalla 2021-05-19 a las 15.41.42.png](https://cdn.hashnode.com/res/hashnode/image/upload/v1621431714596/A-WdFE7Wt.png?auto=compress,format&format=webp)

### This is better

![Captura de pantalla 2021-05-19 a las 15.42.26.png](https://cdn.hashnode.com/res/hashnode/image/upload/v1621431756728/soliUUdwD.png?auto=compress,format&format=webp)

### How?

First change the top level folder and update _services.yaml_

![Captura de pantalla 2021-05-19 a las 15.47.22.png](https://cdn.hashnode.com/res/hashnode/image/upload/v1621432066247/t8oG-SApW.png?auto=compress,format&format=webp)

Then move your entities and change **doctrine.yaml**

![Captura de pantalla 2021-05-19 a las 15.49.46.png](https://cdn.hashnode.com/res/hashnode/image/upload/v1621432200940/TtHCOA_hi.png?auto=compress,format&format=webp)

Finally move Repositories and **update all namespaces**.

Now we have our module divides in **Domain**, where our entities live, **Aplication** for our Use Cases and **Infrastructure** for our external services (like the database)

### Why?

Now each module can grow independently and maybe even extract it to an external service.

You can run **php ./vendor/bin/phpunit** to run the tests.

> Symfony tip completed! Check the [final code](https://github.com/albertobeiz/symfony-tips/tree/14)!

Next Tip -> [Symfony Tips #15 - Dispatch Domain Events](https://github.com/albertobeiz/symfony-tips/tree/15)

Previous Tip -> [Symfony Tips #13 - Do validation in your setters](https://github.com/albertobeiz/symfony-tips/tree/13)
