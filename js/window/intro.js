
export default {
    props: {
        gameModes: Array,
        colors: Array,
        onChangeMode: Function,
        onChangeColor: Function,
    },
    template: `<div>
        <select @change="onChangeMode">
            <option disabled selected>Choose game mode</option>
            <option v-for="item of gameModes" :value="item.value">{{item.title}}</option>
        </select>
        <select @change="onChangeColor">
            <option disabled selected>Choose color</option>
            <option v-for="item of colors" :value="item">{{item}}</option>
        </select>
    </div>
    `
};
