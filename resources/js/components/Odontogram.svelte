<script lang="ts">
    /**
     * Odontogram Digital — SVG Interactive Tooth Chart
     * FDI (Fédération Dentaire Internationale) numbering system
     * Upper Right: 11-18, Upper Left: 21-28, Lower Left: 31-38, Lower Right: 41-48
     */

    type Condition =
        | 'healthy'
        | 'caries'
        | 'filling'
        | 'crown'
        | 'missing'
        | 'root_canal'
        | 'implant'
        | 'bridge';

    type ToothData = {
        tooth_number: number;
        condition: Condition;
        surface?: string | null;
        notes?: string | null;
    };

    let {
        teeth = [],
        readonly = false,
        ontoothclick,
    }: {
        teeth?: ToothData[];
        readonly?: boolean;
        ontoothclick?: (
            toothNumber: number,
            currentCondition: Condition,
        ) => void;
    } = $props();

    // Build a map of tooth conditions
    const toothMap = $derived(() => {
        const map = new Map<number, ToothData>();
        teeth.forEach((t) => map.set(t.tooth_number, t));
        return map;
    });

    function getCondition(num: number): Condition {
        return toothMap().get(num)?.condition ?? 'healthy';
    }

    const conditionColors: Record<Condition, string> = {
        healthy: '#4ade80', // green
        caries: '#ef4444', // red
        filling: '#3b82f6', // blue
        crown: '#f59e0b', // amber
        missing: '#6b7280', // gray
        root_canal: '#a855f7', // purple
        implant: '#06b6d4', // cyan
        bridge: '#f97316', // orange
    };

    const conditionLabels: Record<Condition, string> = {
        healthy: 'Sehat',
        caries: 'Karies',
        filling: 'Tambalan',
        crown: 'Crown',
        missing: 'Hilang',
        root_canal: 'Saluran Akar',
        implant: 'Implan',
        bridge: 'Bridge',
    };

    // Tooth positions for the SVG layout
    // Upper row: right to left (18-11, 21-28)
    const upperTeeth = [
        18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28,
    ];
    // Lower row: right to left (48-41, 31-38)
    const lowerTeeth = [
        48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38,
    ];

    function handleToothClick(num: number) {
        if (readonly) return;
        ontoothclick?.(num, getCondition(num));
    }

    // Determine if a tooth is a molar (wider), premolar, canine, or incisor
    function getToothWidth(num: number): number {
        const unit = num % 10;
        if (unit >= 6) return 42; // molars
        if (unit >= 4) return 36; // premolars
        return 30; // canines + incisors
    }

    function getToothHeight(num: number): number {
        const unit = num % 10;
        if (unit >= 6) return 48;
        if (unit >= 4) return 44;
        if (unit === 3) return 50; // canines are taller
        return 40;
    }
</script>

<div class="space-y-4">
    <!-- Legend -->
    <div class="flex flex-wrap gap-3 text-xs">
        {#each Object.entries(conditionLabels) as [key, label]}
            <div class="flex items-center gap-1.5">
                <div
                    class="h-3 w-3 rounded-sm"
                    style="background-color: {conditionColors[
                        key as Condition
                    ]}"
                ></div>
                <span class="text-muted-foreground">{label}</span>
            </div>
        {/each}
    </div>

    <!-- Odontogram SVG -->
    <div class="overflow-x-auto rounded-lg border bg-card p-4">
        <svg
            viewBox="0 0 640 280"
            class="mx-auto w-full max-w-2xl"
            xmlns="http://www.w3.org/2000/svg"
        >
            <!-- Center line -->
            <line
                x1="320"
                y1="15"
                x2="320"
                y2="265"
                stroke="currentColor"
                stroke-opacity="0.15"
                stroke-width="1"
                stroke-dasharray="4,4"
            />

            <!-- Labels -->
            <text
                x="160"
                y="12"
                text-anchor="middle"
                class="fill-muted-foreground text-[10px]">Kanan Atas</text
            >
            <text
                x="480"
                y="12"
                text-anchor="middle"
                class="fill-muted-foreground text-[10px]">Kiri Atas</text
            >
            <text
                x="160"
                y="278"
                text-anchor="middle"
                class="fill-muted-foreground text-[10px]">Kanan Bawah</text
            >
            <text
                x="480"
                y="278"
                text-anchor="middle"
                class="fill-muted-foreground text-[10px]">Kiri Bawah</text
            >

            <!-- Upper teeth -->
            {#each upperTeeth as num, i}
                {@const x = 20 + i * 37.5}
                {@const w = getToothWidth(num)}
                {@const h = getToothHeight(num)}
                {@const condition = getCondition(num)}
                {@const color = conditionColors[condition]}

                <g
                    transform="translate({x}, 25)"
                    class={readonly ? '' : 'cursor-pointer'}
                    role={readonly ? undefined : 'button'}
                    tabindex={readonly ? undefined : 0}
                    onclick={() => handleToothClick(num)}
                    onkeydown={(e) => {
                        if (e.key === 'Enter') handleToothClick(num);
                    }}
                >
                    <!-- Tooth body -->
                    <rect
                        x={-(w / 2 - 18)}
                        y="0"
                        width={w}
                        height={h}
                        rx="4"
                        fill={color}
                        fill-opacity={condition === 'missing' ? 0.3 : 0.85}
                        stroke={color}
                        stroke-width="1.5"
                        class={readonly
                            ? ''
                            : 'transition-all hover:fill-opacity-100 hover:stroke-2'}
                    />
                    {#if condition === 'missing'}
                        <line
                            x1={-(w / 2 - 18)}
                            y1="0"
                            x2={-(w / 2 - 18) + w}
                            y2={h}
                            stroke={color}
                            stroke-width="1.5"
                        />
                        <line
                            x1={-(w / 2 - 18) + w}
                            y1="0"
                            x2={-(w / 2 - 18)}
                            y2={h}
                            stroke={color}
                            stroke-width="1.5"
                        />
                    {/if}
                    <!-- Root lines (upper teeth roots go down) -->
                    <line
                        x1="15"
                        y1={h}
                        x2="12"
                        y2={h + 20}
                        stroke={color}
                        stroke-width="1"
                        stroke-opacity="0.5"
                    />
                    <line
                        x1="21"
                        y1={h}
                        x2="24"
                        y2={h + 20}
                        stroke={color}
                        stroke-width="1"
                        stroke-opacity="0.5"
                    />
                    <!-- Tooth number -->
                    <text
                        x="18"
                        y={h + 35}
                        text-anchor="middle"
                        class="fill-foreground text-[9px] font-medium"
                        >{num}</text
                    >
                </g>
            {/each}

            <!-- Lower teeth -->
            {#each lowerTeeth as num, i}
                {@const x = 20 + i * 37.5}
                {@const w = getToothWidth(num)}
                {@const h = getToothHeight(num)}
                {@const condition = getCondition(num)}
                {@const color = conditionColors[condition]}

                <g
                    transform="translate({x}, {230 - h})"
                    class={readonly ? '' : 'cursor-pointer'}
                    role={readonly ? undefined : 'button'}
                    tabindex={readonly ? undefined : 0}
                    onclick={() => handleToothClick(num)}
                    onkeydown={(e) => {
                        if (e.key === 'Enter') handleToothClick(num);
                    }}
                >
                    <!-- Root lines (lower teeth roots go up) -->
                    <line
                        x1="15"
                        y1="0"
                        x2="12"
                        y2={-20}
                        stroke={color}
                        stroke-width="1"
                        stroke-opacity="0.5"
                    />
                    <line
                        x1="21"
                        y1="0"
                        x2="24"
                        y2={-20}
                        stroke={color}
                        stroke-width="1"
                        stroke-opacity="0.5"
                    />
                    <!-- Tooth number -->
                    <text
                        x="18"
                        y={-25}
                        text-anchor="middle"
                        class="fill-foreground text-[9px] font-medium"
                        >{num}</text
                    >
                    <!-- Tooth body -->
                    <rect
                        x={-(w / 2 - 18)}
                        y="0"
                        width={w}
                        height={h}
                        rx="4"
                        fill={color}
                        fill-opacity={condition === 'missing' ? 0.3 : 0.85}
                        stroke={color}
                        stroke-width="1.5"
                        class={readonly
                            ? ''
                            : 'transition-all hover:fill-opacity-100 hover:stroke-2'}
                    />
                    {#if condition === 'missing'}
                        <line
                            x1={-(w / 2 - 18)}
                            y1="0"
                            x2={-(w / 2 - 18) + w}
                            y2={h}
                            stroke={color}
                            stroke-width="1.5"
                        />
                        <line
                            x1={-(w / 2 - 18) + w}
                            y1="0"
                            x2={-(w / 2 - 18)}
                            y2={h}
                            stroke={color}
                            stroke-width="1.5"
                        />
                    {/if}
                </g>
            {/each}
        </svg>
    </div>
</div>
