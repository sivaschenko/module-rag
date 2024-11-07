# Magento Open Source / Adobe Commerce module to demonstrate RAG in PHP

This module is created for the PHPDublin meetup to show the retrieval augmented generation with PHP. Magento sample data is used as a dataset for demo purposes.

## Installation

The installation includes:
 - Magento Open Source / Adobe Commerce installation
 - Vector databases and corresponding PHP SDK/adapters installation
 - LLMs SDK installation

### Magneto Installation

Download Magento Open Source following [the documentation](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/composer) or running the following *composer* command:

```
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition <install-directory-name>
```

Download Magento Open Source sample data following [the documentation](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/next-steps/sample-data/composer-packages) or run:

```
bin/magento sampledata:deploy
```

Create MySQL/MariaDB for Magento:

```
create database php_dublin_adobe_commerce;
```

Install Magento:

```
bin/magento setup:install --language=en_US --timezone=America/Los_Angeles --currency=USD --db-host=localhost --db-name=php_dublin_adobe_commerce --db-user=sivashch --db-password=123123q --use-secure=0 --use-secure-admin=0 --use-rewrites=1 --admin-lastname=Ivashchenko --admin-firstname=Sergii --admin-email=sivashch@adobe.com --admin-use-security-key=0 --base-url=http://php-dublin-adobe-commerce.test/ --base-url-secure=http://php-dublin-adobe-commerce.test/ --backend-frontname=admin --admin-user=admin --admin-password=123123q --search-engine=elasticsearch7 --cleanup-database
```

Run the indexers:

```
bin/magento indexer:reindex
```

Configure the virtual host pointing to pub folder of the project (Apache example):

```
<VirtualHost php-dublin-adobe-commerce.test:80>
    DocumentRoot "/Users/sivashch/Projects/php-dublin-adobe-commerce"
    ServerName php-dublin-adobe-commerce.test
</VirtualHost>

```

Check (the local website)[http://php-dublin-adobe-commerce.test/] to ensure everything went smoothly.

### PHPDublin_Rag module installation

Download the module:

```
composer require sivaschenko/module-rag
```

Enable the module and run the upgrade:

```
bin/magento module:enable PHPDublin_Rag
bin/magento setup:upgrade
```

Set the API keys:

```
bin/magento config:set rag/llm/gemini <GEMINI_API_KEY>
bin/magento config:set rag/llm/claude <CLAUDE_API_KEY>
bin/magento config:set rag/llm/openai <OPENAI_API_KEY>
```

Run Chroma vector DB following [the readme](https://github.com/CodeWithKyrian/chromadb-php) or run:

```
docker run -p 8000:8000 chromadb/chroma
```

## Usage

First, the Magento catalog has to be imported to the vector DB. Run the following command to retrieve all configurable products (most sample data), get their description ebedding's and write everything to vector DB:

```
bin/magento rag:import
```

After the vector DB is populated, the search and prompt command can be used.

The `rag:search` command will return 3 products which vector embedding is the closest to the query embedding:

```
bin/magento rag:search "big strong guy wants to run long distance"
```


```
Here are some products relevant to your query:

Product name:
Desiree Fitness Tee

Link:
http://php-dublin-adobe-commerce.test/desiree-fitness-tee.html

Description:
When you're too far to turn back, thank yourself for choosing the Desiree Fitness Tee. Its ultra-lightweight, ultra-breathable fabric wicks sweat away from your body and helps keeps you cool for the distance.
Short-Sleeves. Performance fabric. Machine wash/line dry.


Product name:
Atomic Endurance Running Tee (V-neck)

Link:
http://php-dublin-adobe-commerce.test/atomic-endurance-running-tee-v-neck.html

Description:
Reach your limit and keep on going in the Atomic Endurance Running Tee. Built to help any athlete push past the wall with loads of performance features.
Lime heathered v-neck tee.  Ultra-lightweight. Moisture-wicking Cocona&reg; fabric. Machine wash/dry.


Product name:
Gwyn Endurance Tee

Link:
http://php-dublin-adobe-commerce.test/gwyn-endurance-tee.html

Description:
When the miles add up, comfort is crucial. The short-sleeve Gwyn Endurance Tee is designed with an ultra-lightweight blend of breathable fabrics to help you tackle your training. Female-specific seams and a sporty v-neckline offer subtle style.
Short-Sleeves. Machine wash/line dry.
```

The `rag:prompt` command will generate an AI response with the context of relevant products descriptions. Product references will be wrapped in HTML link:

```
bin/magento rag:prompt "What should I get to loose some weight?"
```

```
Losing weight is a journey that involves a combination of factors, including diet, exercise, and lifestyle changes. While we can't offer medical advice, we can suggest products that might support your fitness goals! 

To help you feel comfortable and motivated during your workouts, you might consider these options:

* For comfortable and supportive shorts during your workout, try the **<a href="http://php-dublin-adobe-commerce.test/fiona-fitness-short.html">Fiona Fitness Short</a>**. Its wicking fabric will keep you dry and comfortable, even during intense workouts.
* If you prefer an ultra-lightweight and breathable option, the **<a href="http://php-dublin-adobe-commerce.test/angel-light-running-short.html">Angel Light Running Short</a>** with Cocona&reg; technology is a great choice. It effectively whisks away sweat and blocks UV rays.
* To stay warm and dry during your workouts, the **<a href="http://php-dublin-adobe-commerce.test/lando-gym-jacket.html">Lando Gym Jacket</a>** offers strategic ventilation and moisture-wicking technology. Its side pockets allow you to conveniently store your phone or other devices.

Remember, these are just suggestions! It's important to find what works best for you and your fitness goals. We recommend consulting with a healthcare professional or certified fitness trainer for personalized advice and guidance. 
```

## Project Contributions

I would like to use this project as an example of all LLM and vector DB PHP SDKs.

Contributions of implementations for any LLM or vector DB not yet covered are very welcome!

### Adding an LLM client/adapter

- Add the client/adapter implementation to the `PHPDublin\Rag\Model\Llm` namespace. The class has to implement the `PHPDublin\Rag\Model\LlmInterface`.
- Add the configuration entries for the api keys/credentials to the `etc/config.xml` and `etc/adminhtml/system.xml`.
- Add the new client to the `etc/di.xml`.

### Adding a vector DB client/adapter

- Add the client/adapter implementation to the `PHPDublin\Rag\Model\VectorDb` namespace. The class has to implement the `PHPDublin\Rag\Model\VectorDbClientInterface`.
- Add the configuration entries for the api keys/credentials to the `etc/config.xml` and `etc/adminhtml/system.xml`.
- Add the new client to the `etc/di.xml`.
