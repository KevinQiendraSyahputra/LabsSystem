import { cn } from "@/lib/utils"

function Skeleton({
  className,
  ...props
}: React.HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      className={cn("animate-pulse rounded-md bg-muted/60 bg-slate-200 dark:bg-slate-800", className)}
      {...props}
    />
  )
}

export { Skeleton }
