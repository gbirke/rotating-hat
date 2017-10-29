<template>
    <div id="test-app" class="card">
        <div class="card-body">

            <div class="event" v-for="event in events">
                <div class="event__date datebox bg-primary text-white">
                    <div class="datebox__day">
                        {{event.start.getDate()}}
                    </div>
                    <div class="datebox__month">
                        {{event.start | monthName }}
                    </div>
                </div>
                <div class="event__label">
                    {{event.label}}
                </div>
                <div class="event__date datebox bg-primary text-white">
                    <div class="datebox__day">
                        {{event.end.getDate()}}
                    </div>
                    <div class="datebox__month">
                        {{event.end | monthName }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
    export default {
        data () {
            return {
                events: [
                    {
                        label: 'First Deployer of the day',
                        start: new Date( '2017-10-02'),
                        end: new Date( '2017-11-07' )
                    },
                    {
                        label: 'Second Deployer of the day',
                        start: new Date( '2017-05-07'),
                        end: new Date( '2017-07-14' )
                    }
                ]
            }
        }
    }
</script>
<style>

    .event {
        display: flex;
        margin-bottom: 15px;
        box-shadow: 5px 5px rgba(0,0,0,.2);;
        background: rgba(0,0,0,.03);
    }

    .event__date {
        flex: 1;
    }

    .event__label {
        flex: 2;
        padding: 0.3em 1em;
        font-weight: bold;
        color: rgba(0,0,0,.66);
    }
    
    .datebox__day {
        font-size: 2em;
        text-align: center;
        font-weight: bold;
    }

    .datebox__month {
        text-align: center;
        border-top: dotted white 1px;
        text-transform: uppercase;
    }

</style>