#!/bin/sh

function recrud() {
    echo $1
    lc=$(echo $1 |  tr '[:upper:]' '[:lower:]')

    rm -f src/AppBundle/Controller/${1}Controller.php
    rm -f src/AppBundle/Resources/views/$2/*
    rm -f src/AppBundle/Form/${1}Type.php
    rm -f src/AppBundle/Tests/Controller/${1}ControllerTest.php
    mkdir -p src/AppBundle/Resources/views/$2

    ./bin/console doctrine:generate:crud \
                  --quiet \
                  --no-interaction \
                  --entity=AppBundle:$1 \
                  --route-prefix=$2 \
                  --with-write \
                  --format=annotation \
                  --overwrite

    mv app/Resources/views/$lc/* src/AppBundle/Resources/views/$2/
    rmdir app/Resources/views/$lc
};

#recrud City                     city
#recrud Event                    event
#recrud EventCategory            event_category
#recrud Ledger                   ledger
#recrud Location                 location
#recrud LocationCategory         location_category
#recrud Notary                   notary
#recrud Person                   person
#recrud Race                     race
recrud Relationship             relationship
#recrud RelationshipCategory     relationship_category
#recrud Residence                residence
#recrud Transaction              transaction
#recrud TransactionCategory      transaction_category
#recrud Witness                  witness
#recrud WitnessCategory          witness_category
