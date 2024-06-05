<div>
    <div x-data="clockComponent()" class="clock-container">
        <div class="clock">
            <div>
                <div class="info-clock date-clock" x-text="date"></div>
                <div class="info-clock day-clock" x-text="day"></div>
            </div>
            <div class="dot-clock"></div>
            <div>
                <div class="hour-hand" :style="{ transform: `rotate(${hourDeg}deg)` }"></div>
                <div class="minute-hand" :style="{ transform: `rotate(${minuteDeg}deg)` }"></div>
                <div class="second-hand" :style="{ transform: `rotate(${secondDeg}deg)` }"></div>
            </div>
            <div>
                <span-clock class="h3-clock">3</span-clock>
                <span-clock class="h6-clock">6</span-clock>
                <span-clock class="h9-clock">9</span-clock>
                <span-clock class="h12-clock">12</span-clock>
            </div>
            <template x-for="i in 60">
                <div class="diallines-clock" :style="{ transform: `rotate(${6 * i}deg)` }"></div>
            </template>
        </div>
    </div>

    <style>
       
        .clock {
            background: #ececec;
            width: 250px;
            height: 250px;
            margin: 8% auto 0;
            border-radius: 50%;
            border: 5px solid #333;
            position: relative;
            box-shadow: 0 2vw 4vw -1vw rgba(0,0,0,0.8);
        }

        .dot-clock {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ccc;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            position: absolute;
            z-index: 10;
            box-shadow: 0 2px 4px -1px black;
        }

        .hour-hand {
            position: absolute;
            z-index: 5;
            width: 4px;
            height: 33px;
            background: #333;
            top: 79px;
            transform-origin: 50% 100%; /* Corrected */
            left: 50%;
            margin-left: -2px;
            border-top-left-radius: 50%;
            border-top-right-radius: 50%;
        }

        .minute-hand {
            position: absolute;
            z-index: 6;
            width: 4px;
            height: 65px;
            background: #666;
            top: 46px;
            left: 50%;
            margin-left: -2px;
            border-top-left-radius: 50%;
            border-top-right-radius: 50%;
            transform-origin: 50% 100%; /* Corrected */
        }

        .second-hand {
            position: absolute;
            z-index: 7;
            width: 2px;
            height: 85px;
            background: gold;
            top: 26px;
            left: 50%;
            margin-left: -1px;
            border-top-left-radius: 50%;
            border-top-right-radius: 50%;
            transform-origin: 50% 100%; /* Corrected */
        }

        span-clock {
            display: inline-block;
            position: absolute;
            color: #333;
            font-size: 22px;
            font-family: 'Poiret One';
            font-weight: 700;
            z-index: 4;
        }

        .h12-clock {
            top: 20px;
            left: 50%;
            margin-left: -9px;
        }
        .h3-clock {
            top: 100px;
            right: 30px;
        }
        .h6-clock {
            bottom: 20px;
            left: 50%;
            margin-left: -5px;
        }
        .h9-clock {
            left: 30px;
            top: 100px;
        }

        .diallines-clock {
            position: absolute;
            z-index: 2;
            width: 2px;
            height: 15px;
            background: #666;
            left: 50%;
            margin-left: -1px;
            transform-origin: 50% 120px;
        }
        .diallines-clock:nth-of-type(5n) {
            position: absolute;
            z-index: 2;
            width: 3px;
            height: 25px;
            background: #666;
            left: 50%;
            margin-left: -1px;
            transform-origin: 50% 120px;
        }

        .info-clock {
            position: absolute;
            width: 100px;
            height: 20px;
            border-radius: 7px;
            background: #ccc;
            text-align: center;
            line-height: 20px;
            color: #000;
            font-size: 11px;
            font-family: "Poiret One";
            font-weight: 700;
            z-index: 3;
            letter-spacing: 3px;
            margin-left: -50px;
            left: 50%;
        }
        .date-clock {
            top: 50px;
        }
        .day-clock {
            top: 150px;
        }
    </style>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('clockComponent', () => ({
            date: '',
            day: '',
            hourDeg: 0,
            minuteDeg: 0,
            secondDeg: 0,
            init() {
                this.updateClock();

                setInterval(() => {
                    this.updateClock();
                }, 1000);
            },
            updateClock() {
                const weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                const d = new Date();
                const h = d.getHours();
                const m = d.getMinutes();
                const s = d.getSeconds();
                const date = d.getDate();
                let month = d.getMonth() + 1;
                const year = d.getFullYear();
                this.day = weekday[d.getDay()];
                if (month < 10) {
                    month = "0" + month;
                }
                this.date = `${date}/${month}/${year}`;
                this.hourDeg = h * 30 + m * (360 / 720);
                this.minuteDeg = m * 6 + s * (360 / 3600);
                this.secondDeg = s * 6;
            }
        }));
    });
</script>
@endpush