# Pokedex

## Description:

This project is an http web API that may be used to retrieve Pokedex related information and do establish trainer accounts for maintaining capture information. Trainers may use this information to assist themselves in learning about and tracking their captured Pokemon as they seek to catch them all. The project is built in php using the laravel web framework.

## Endpoints:

### GET /api/pokemon?page=#&count=#
Returns a paginated list of Pokemon information. Data for each included Pokemon is kept basic. Public endpoint. Does not require authentication.

Accepts two query parameter:

- page: The desired page number. Must be greater than 0 and less than the number of pages available to the current count. Defaults to 1 if not provided.
- count: The desired number of items per page.  Must be 5, 10, 20, 25, 50 or 100. Defaults to 10 if not provided.

Json Response Example:

{
    "success": true,
    "message": "Page 1 of the paginate list of pokemon defined with 10 pokemon per page was successfully retrieved.",
    "links": {
        "first": "http://localhost:8000/api/pokemon?page=1&count=10",
        "previous": "http://localhost:8000/api/pokemon?page=1&count=10",
        "self": "http://localhost:8000/api/pokemon?page=1&count=10",
        "next": "http://localhost:8000/api/pokemon?page=2&count=10",
        "last": "http://localhost:8000/api/pokemon?page=56&count=10"
    },
    "data": {
        "page_number": 1,
        "total_pages": 56,
        "count": 10,
        "pokemon": [
            {
                "id": 1,
                "name": "Bulbasaur",
                "types": [
                    "poison",
                    "grass"
                ],
                "description": "Bulbasaur can be seen napping in bright sunlight.\r\nThere is a seed on its back. By soaking up the sun’s rays,\r\nthe seed grows progressively larger.",
                "links": {
                    "self": "http://localhost:8000/api/pokemon/1"
                }
            },
            ...
        ]
    }
}


### GET /api/pokemon/{$id}
Returns the full data on the pokemon with the provided {$id}. Public endpoint. Does not require authentication.

Json Response Example:

{
    "success": true,
    "message": "Information for Nidorino, id = 33, successfully retrieved.",
    "links": {
        "self": "http://localhost:8000/api/pokemon/33"
    },
    "data": {
        "id": 33,
        "name": "Nidorino",
        "types": [
            "poison"
        ],
        "weight": 195,
        "height": 9,
        "abilities": [
            "hustle",
            "rivalry",
            "poison-point"
        ],
        "egg_groups": [
            "ground",
            "monster"
        ],
        "stats": {
            "hp": 61,
            "speed": 65,
            "attack": 72,
            "defense": 57,
            "special-attack": 55,
            "special-defense": 55
        },
        "genus": "Poison Pin Pokémon",
        "description": "Nidorino has a horn that is harder than a diamond. If it senses\r\na hostile presence, all the barbs on its back bristle up at once,\r\nand it challenges the foe with all its might.",
        "created_at": "2020-01-07 19:05:32",
        "updated_at": "2020-01-07 19:05:32"
    }
}


### POST /api/register
Creates a new trainer account with the provided form data. Returns an initial access token. Public endpoint. Does not require authentication.

Accepts 3 pieces of form data:

- name: The name of the trainer. Required. Must be at least 3 characters in length.
- email: The email of the trainer. Required. Must be an email. Cannot already be used by another trainer.
- password: The password the trainer would like to use to gain access to their account. Required. Must be at least 6 characters in length.

Json Response Example:

{
    "success": true,
    "message": "A trainer account was successfully created using the provided information.",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYmE4YmIyZjI5Njc5ZjliYjkxNTA1ODI0Y2Q0MjE5MmRjNzY1MjI3OTIzNDY1ZmY4ZTRmYjBlOTNlOWFhM2Q0ODE3NTQ4ZTA3MjIxOGE1YWQiLCJpYXQiOjE1Nzg1MzY1ODQsIm5iZiI6MTU3ODUzNjU4NCwiZXhwIjoxNTc4NTQwMTg0LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.Cxmk6qTJVhYOxByIof2CqyQ5EtJqzYQWVmZdhYqHcPwTKfFnQKpvBk6iZlODO04EtQ43IDoNRQUkC44WAZPTmOnd5VqPvPv6Y1OzHaKg32KQUG8PqXQEnStmAaQFjHaTtJyO83_N2gtLpH958zNvJ3Ris8KH9yglfyAxs6KQZLpP3FFyUYE7yeCMDAinPU2J8G9NLiwtWPMeDrFpYCic1kvwPsmKsvLw1E84vxppdKBLlbbACD1NFJ_XnYfcZYr49OP_TaDBzTlSLtHJ1uzMeEYOXG_1MUYSGUTADrByfkTMNmxO3FRa4a2r3Z61qVFQCOARdqi99bIVMWGRyQAIW9Idk68uSqEWsfmobJHU6HA5FYgeuVz43AxBiiAz3j_K1DldrIeN0t8BOW5w9pXG_MB-4XTFXingjudY_bNAzVG1va5Zz4uyC1qb4fAXL7xPxSdtpPWnX0K2tkvWfF9-3R0csigVqTV3qfXBpPmgbVr0WTsPSmFcbeBCTSxK6CNsDC4VkghpbPvX3qiO_Kv6qtituFgB5ZFotYq8EBggp8_YVadTPvd5HjpA-HGMN1RVOGp6S8_PeF8xAaBBvkmRXKiv-iU8y0SIahpJdUUAb6iJ6pX4f8Ike_hgfGHGBwCxQF_xtkP_eV-ZaaFhph21T0ZLILS-oVtZeu0vBE1USv0"
}


### POST /api/login
Verifies a trainers authorization credentials and returns a new access token. Public endpoint. Does not require authentication.

Accepts 2 pieces of form data:

- email: The email of the trainer. Required.
- password: The password of the trainer. Required.

Json Response Example:

{
    "success": true,
    "message": "Provided trainer credentials were accepted and a new access token has been provided.",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNWY4ZDdhMzBjOTQ3Mjg3YzIxNzkxYjk4NmQyY2VhZjkwMzBlNzU3MTc4NTk0Y2Q2NTJhM2Q1NDgwOWI4Y2QwNTIwYjg2NmJlZWUzM2FkMDUiLCJpYXQiOjE1Nzg1MzY4MTcsIm5iZiI6MTU3ODUzNjgxNywiZXhwIjoxNTc4NTQwNDE3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.j5Zqmlc9P4bqzRVtI0wiN1k9Yg4HCi92k6Z8BastYmqjAIB3jXwDX-u_dUVF1oqJMvOFlXKSFNckBNbfFArwBvC2jZZV57-730LVIsn_KH5CZif9wffo3zzHmABzzsDa76KzTF6qWB416xZI1oWcH7dW6JA7aFYwFHUbJQ5MKBljY13y-KHZ92_BnyGfg5TG-XSCivGcwdLsycjIkZGmD3yiZnI6oTKiO-kzomhypnhjYqK8gkdlteTy8nGXDQSdkmRjVagQC181PImairZGq9AmbdHOZNYTcSLkKJaU_oAyLhMLWOF91bq90i94Hiu4bLBPjFbctN6TMeJZN2hs7JgYMqu40oVD5WrGFfUrLFMBvDEiwAzfECRhqBqclr8SV2uC6w3SqCZvRRx0d0j1M86mX_jgmON83JOaumruL8pvIt1m3kCeUegjSvT1syk4HpdVv7lIjQivlJT_6UvInukaZfPusPC63Khz72nLLa2fBYeeB20HB11ytzW8jFpznI5UncQbFOTcJV4JZTKjnH3-55RPX-9-4MquFwHZGkRv5vzAn7YEsvhJYoqejgjvGexqjWt1PeTlc84mwM0rHAzj4mMDB-D0hoFna4831cyw7bzWqmETXyIbJZcxgk2wcVVCrJE1n_uWp865xfGG0sALvWiwwT0avBMcvOywzV0"
}


### GET /api/user
Returns the account information for the authorized trainer. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Json Response Example:

{
    "success": true,
    "message": "Information for the desired trainer has been successfully retrieved.",
    "trainer": {
        "id": 2,
        "name": "Ash Ketchum",
        "email": "ash.ketchum@example.com",
        "email_verified_at": null,
        "created_at": "2020-01-09 02:23:04",
        "updated_at": "2020-01-09 02:23:04"
    }
}


### GET /api/captures
Returns a list of all captures that have been registered by the currently authenticated trainer. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Json Response Example:

{
    "success": true,
    "message": "Pokemon captures for Ash Ketchum were successfully retrieved.",
    "data": {
        "captured_pokemon": [
            {
                "capture": {
                    "id": 4,
                    "user_id": 2,
                    "pokemon_id": 25,
                    "created_at": "2020-01-09 02:38:06",
                    "updated_at": "2020-01-09 02:38:06"
                },
                "pokemon": {
                    "id": 25,
                    "name": "Pikachu",
                    "links": {
                        "self": "http://localhost:8000/api/pokemon/25"
                    }
                }
            },
            ...
        ]
    }
}


### GET /api/captures/{$pokemon_id}
Returns a list of all captures that have been registered by the currently authenticated trainer. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Json Response Example:

{
    "success": true,
    "message": "Capture for Pikachu was successfully retrieved for trainer Ash Ketchum.",
    "data": {
        "capture": {
            "id": 4,
            "user_id": 2,
            "pokemon_id": 25,
            "created_at": "2020-01-09 02:38:06",
            "updated_at": "2020-01-09 02:38:06"
        },
        "pokemon": {
            "id": 25,
            "name": "Pikachu",
            "links": {
                "self": "http://localhost:8000/api/pokemon/25"
            }
        }
    }
}


### POST /api/captures
Creates a new capture by the currently authorized user for the pokemon with the "pokemon_id" provided in the form data. The sets the pokemon as captured by the trainer. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Accepts 1 pieces of form data:

- pokemon_id: The id of the pokemon that the trainer would like to set as captured.

Json Response Example:

{
    "success": true,
    "message": "Capture for Pikachu was successfully created for trainer Ash Ketchum.",
    "data": {
        "capture": {
            "pokemon_id": "25",
            "user_id": 2,
            "updated_at": "2020-01-09 02:38:06",
            "created_at": "2020-01-09 02:38:06",
            "id": 4
        },
        "pokemon": {
            "id": 25,
            "name": "Pikachu",
            "links": {
                "self": "http://localhost:8000/api/pokemon/25"
            }
        }
    }
}


### DELETE /api/captures/{pokemon_id}
Deletes the capture of the Pokemon with the provided pokemon_id by the authorized trainer. This marks the Pokemon as uncaptured by the trainer. A capture of that Pokemon must exist. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Json Response Example:

{
    "success": true,
    "message": "Capture of Butterfree has been removed for trainer Ash Ketchum.",
    "data": {
        "capture": {
            "id": 10,
            "user_id": 2,
            "pokemon_id": 12
        }
    }
}


### DELETE /api/evaluation
Returns information on the authorized users progress in completing the Pokedex. Includes completed stats, oldest capture, latest capture, and completion certificate. Private endpoint. Requires active access token. Token msut be provided in the request header "Authorization: Bearer {token}".

Json Response Example:

{
    "success": true,
    "message": "Pokedex evaluation successfully retrieved for Ash Ketchum.",
    "data": {
        "captured_percentage": 1.08,
        "pokemon_captured": "6 of 553",
        "captured_count": 6,
        "pokemon_count": 553,
        "oldest_capture": {
            "capture": {
                "id": 4,
                "user_id": 2,
                "pokemon_id": 25,
                "created_at": "2020-01-09 02:38:06",
                "updated_at": "2020-01-09 02:38:06"
            },
            "pokemon": {
                "id": 25,
                "name": "Pikachu",
                "links": {
                    "self": "http://localhost:8000/api/pokemon/25"
                }
            }
        },
        "latest_capture": {
            "capture": {
                "id": 9,
                "user_id": 2,
                "pokemon_id": 11,
                "created_at": "2020-01-09 02:42:16",
                "updated_at": "2020-01-09 02:42:16"
            },
            "pokemon": {
                "id": 11,
                "name": "Metapod",
                "links": {
                    "self": "http://localhost:8000/api/pokemon/11"
                }
            }
        },
        "certificate": {
            "trainer": "Ash Ketchum",
            "achieved": false,
            "text": ""
        }
    }
}