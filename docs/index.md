# personnal-data-bundle

Manage your Personnal Data for GPDR compliancy.

## Features

* Declare your personnal data in your schema with Doctrine annotations.
* Dispatch events when your application collects or exposes personnal datas.
* Allow data to be owned by multiples provider (e.g.: Your client, a company, entrust you with some personnal datas about their employees, theses datas are owned by your client company, and the employee.)
* Manage your users consents to ensure you respect their preferences at any time.
* Make a big step towards GPDR compliancy by tracking each data individually.
* Export or Anonymize all data from a particular data provider.

## Features to be implemented

* data register reports
* cron task for intermediate archiving of personnal datas
* cron task for final archiving of personnal datas
* auditor-bundle compatibility (auto delete audit table when anonymizing)
