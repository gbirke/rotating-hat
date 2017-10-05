# Next Steps

## MVP
- [ ] Help texts
- [X] Proper Timezone selection
- [X] Date Picker
- [X] Layout form in columns
- [X] Select box for recurrence: Once (just generate one event for each person), Until ... (Show field for end date ), Forever
- [X] Improve label handling (concat with colon only if not empty)

## Later
- [ ] Calendar+Schedule preview in right column (https://github.com/Serhioromano/bootstrap-calendar, plus https://github.com/simshaun/recurr for generating the dates on the server side)
- [ ] German translation
- [ ] Store settings, provide URL to calendar, URL for calendar for each Person and special "Admin URL" for changing settings.
- [ ] More flexible duration (input field for number instead of fixed to "1")
- [ ] Recur weekly, but only on some days (e.g. monday-friday)
- [ ] Even more spread-out recurrence schemes (e.g. distribute task by-weekly)
- [ ] Hourly duration
- [ ] Printable schedules

## Technical improvements
- [ ] Add CI
- [ ] Edge-to-Edge test
- Webpack:
    - [ ] Uglify production build
    - [ ] Use `extract-loader` for CSS
    - [ ] Enable auto-reloading on dev (--watch + browsersync)
