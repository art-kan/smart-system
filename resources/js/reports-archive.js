!function setupCustomRangePickerToggle() {
    const toggleUI = document.getElementById('toggle-custom-range-picker');
    const customRangePickerUI = document.getElementById('custom-range-picker');
    const quickRangePickerUI = document.getElementById('quick-range-picker');

    customRangePickerUI.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 1, 1) 0s';
    quickRangePickerUI.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 1, 1) 0s';

    const customRangePickerInputTo = customRangePickerUI.querySelector('input[name="to"]');
    if (customRangePickerUI.value == null) customRangePickerInputTo.value = getTodayDate();

    if (quickRangePickerUI.classList.contains('hidden')) toggleUI.classList.add('active');

    toggleUI.addEventListener('click', () => {
        customRangePickerUI.classList.toggle('hidden');
        quickRangePickerUI.classList.toggle('hidden');
        toggleUI.classList.toggle('active');
    });
}();

function getTodayDate() {
    const date = new Date;
    const offset = date.getTimezoneOffset()
    const shiftedDate = new Date(date.getTime() - (offset * 60 * 1000))
    return shiftedDate.toISOString().split('T')[0]
}
