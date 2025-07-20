<script setup lang="ts">
import { computed, ref } from 'vue';

interface TreeData {
  parameters: {
    diameter?: number;
  };
  biomass_agb_kg?: number;
}

const props = defineProps<{
  data: TreeData[];
}>();

const width = 500;
const height = 300;
const margin = { top: 20, right: 20, bottom: 40, left: 60 };

const chartWidth = width - margin.left - margin.right;
const chartHeight = height - margin.top - margin.bottom;

const points = computed(() => {
  if (!props.data || props.data.length === 0) return [];

  const xValues = props.data.map(d => d.parameters.diameter || 0);
  const yValues = props.data.map(d => d.biomass_agb_kg || 0);

  const xMax = Math.max(...xValues);
  const yMax = Math.max(...yValues);

  return props.data.map(d => {
    const x = ((d.parameters.diameter || 0) / xMax) * chartWidth;
    const y = chartHeight - ((d.biomass_agb_kg || 0) / yMax) * chartHeight;
    return { x, y, original: d };
  });
});

const xTicks = computed(() => {
  if (!points.value.length) return [];
  const xMax = Math.max(...props.data.map(d => d.parameters.diameter || 0));
  return Array.from({ length: 6 }, (_, i) => ({
    value: (i * (xMax / 5)).toFixed(0),
    x: i * (chartWidth / 5),
  }));
});

const yTicks = computed(() => {
  if (!points.value.length) return [];
  const yMax = Math.max(...props.data.map(d => d.biomass_agb_kg || 0));
  return Array.from({ length: 6 }, (_, i) => ({
    value: (i * (yMax / 5)).toFixed(0),
    y: chartHeight - i * (chartHeight / 5),
  }));
});
</script>

<template>
  <div class="chart-container">
    <svg :viewBox="`0 0 ${width} ${height}`">
      <g :transform="`translate(${margin.left}, ${margin.top})`">
        <line :x1="0" :y1="chartHeight" :x2="chartWidth" :y2="chartHeight" stroke="#ccc" />
        <line :x1="0" :y1="0" :x2="0" :y2="chartHeight" stroke="#ccc" />

        <g v-for="tick in xTicks" :key="tick.value" class="tick">
          <line :x1="tick.x" :y1="chartHeight" :x2="tick.x" :y2="chartHeight + 6" stroke="#ccc" />
          <text :x="tick.x" :y="chartHeight + 20" text-anchor="middle">{{ tick.value }}</text>
        </g>
        <text class="axis-label" :x="chartWidth / 2" :y="height - 5" text-anchor="middle">DBH (cm)</text>
        
        <g v-for="tick in yTicks" :key="tick.value" class="tick">
          <line :x1="-6" :y1="tick.y" :x2="0" :y2="tick.y" stroke="#ccc" />
          <text :x="-10" :y="tick.y" text-anchor="end" dominant-baseline="middle">{{ tick.value }}</text>
        </g>
        <text class="axis-label" :transform="`translate(-45, ${chartHeight / 2}) rotate(-90)`" text-anchor="middle">AGB (kg)</text>

        <circle v-for="(point, i) in points" :key="i"
          :cx="point.x" :cy="point.y" r="4"
          fill="#4ade80" stroke="#15803d" stroke-width="1"
        />
      </g>
    </svg>
  </div>
</template>

<style scoped>
.chart-container {
  margin-top: 24px;
  background-color: #f8fafc;
  border-radius: 12px;
  padding: 16px;
  max-width: 100%;
}
svg {
  width: 100%;
  height: auto;
}
.tick text {
  font-size: 10px;
  fill: #666;
}
.axis-label {
  font-size: 12px;
  fill: #333;
  font-weight: 500;
}
</style>