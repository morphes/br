import dayjs from 'dayjs';
import 'dayjs/locale/ru';

const formatDate = ({ date, locale = 'ru', format, day }) => {
    dayjs.locale(locale);

    if (day) {
        return dayjs()
            .add(day, 'day')
            .format(format);
    }

    return dayjs(date).format(format);
};

export default formatDate;
