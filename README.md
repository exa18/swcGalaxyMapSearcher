# swcGalaxyMapSearcher
[swCombine GalaxyMap](https://www.swcombine.com/) download localy for easeir/faster access to searched data. Using [API 2.0](https://www.swcombine.com/ws/v2.0/)

![GitHub milestone](https://img.shields.io/github/milestones/progress/exa18/swcGalaxyMapSearcher/1?style=flat-square)
![GitHub milestone](https://img.shields.io/github/milestones/progress/exa18/swcGalaxyMapSearcher/4?style=flat-square)

## MAP
What data are downloaded?

### STAGE 1

#### SECTOR (25)
```
    uid
    name
    controlledby
    knownsystems
    population
```
#### SYSTEM (9)
```
    uid
    name
    population
    controlledby
    location->sector
        uid
    location->coordinates->galaxy
        x
        y
```
#### FACTION (20)
```
    uid
    name
```

## UPDATE

### Performance
```
    STAGE 1
        SECTORs : ~10s
        SYSTEMs : ~25s
        ( FACTION )

    STAGE 2
        PLANETS

    STAGE 3
        CITIES
```

### How and what
Use cron and run script from **_update.php**
```
    STAGE 1 update
        sectors
        systems
        faction
    STAGE 2 update
        planets
        stations
    STAGE 3 update
        cities
```

## API v2.0 links

### Types used

|ID|TYPE-LINK|
|---|---|
|25|[sectors](https://www.swcombine.com/ws/v2.0/galaxy/sectors/)|
|9|[systems](https://www.swcombine.com/ws/v2.0/galaxy/systems/)|
|20|[factions](https://www.swcombine.com/ws/v2.0/factions/)|
|8|[planets](https://www.swcombine.com/ws/v2.0/galaxy/planets/)|
|-|[planet type](https://www.swcombine.com/ws/v2.0/types/planets)|
|7|[cities](https://www.swcombine.com/ws/v2.0/galaxy/cities/)|
|5|[stations](https://www.swcombine.com/ws/v2.0/galaxy/stations)|
|-|[station type](https://www.swcombine.com/ws/v2.0/types/stations)|

### Other types

|ID|TYPE-LINK|
|---|---|
|1|user|
|24|[terrain](https://www.swcombine.com/ws/v2.0/types/terrain)|
|22|[races](https://www.swcombine.com/ws/v2.0/types/races)|
|18|[weapons](https://www.swcombine.com/ws/v2.0/types/weapons)|
|16|[materials](https://www.swcombine.com/ws/v2.0/types/materials)|
|3|[vehicles](https://www.swcombine.com/ws/v2.0/types/vehicles)|
|13|[droids](https://www.swcombine.com/ws/v2.0/types/droids)|
|4|[facilities](https://www.swcombine.com/ws/v2.0/types/facilities)|
|12|[items](https://www.swcombine.com/ws/v2.0/types/items)|
|10|[npcs](https://www.swcombine.com/ws/v2.0/types/npcs)|
|11|[creatures](https://www.swcombine.com/ws/v2.0/types/creatures)|


